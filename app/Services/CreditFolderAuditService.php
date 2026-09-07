<?php

namespace App\Services;

use App\Models\CreditFolderAudit;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreditFolderAuditService
{
    private const IGNORED_ATTRIBUTES = ['created_at', 'updated_at'];

    public function recordCreated(Model $model)
    {
        $values = $this->withoutIgnoredAttributes($model->getAttributes());

        $this->store($model, 'CREATED', null, $values);
    }

    public function recordUpdated(Model $model)
    {
        $newValues = $this->withoutIgnoredAttributes($model->getChanges());

        if (empty($newValues)) {
            return;
        }

        $oldValues = [];
        foreach (array_keys($newValues) as $attribute) {
            $oldValues[$attribute] = $model->getOriginal($attribute);
        }

        $this->store($model, 'UPDATED', $oldValues, $newValues);
    }

    public function recordDeleted(Model $model)
    {
        $values = $this->withoutIgnoredAttributes($model->getAttributes());

        $this->store($model, 'DELETED', $values, null);
    }

    private function store(Model $model, $event, $oldValues, $newValues)
    {
        $context = $this->resolveCreditContext($model);
        $action = $this->resolveAction($model, $event, $oldValues, $newValues);
        $user = Auth::user();

        CreditFolderAudit::create([
            'credit_folder_header_id' => $context['header_id'],
            'credit_folder_detail_id' => $context['detail_id'],
            'company_id' => $model->company_id,
            'code_folder_header' => $context['code'],
            'source' => $context['source'],
            'event' => $event,
            'action' => $action,
            'description' => $this->description($model, $action),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $user ? $user->id : null,
            'user_name' => $this->userName($user),
            'ip_address' => $this->requestValue('ip'),
            'user_agent' => $this->requestValue('userAgent'),
            'url' => $this->requestValue('fullUrl'),
        ]);
    }

    private function resolveCreditContext(Model $model)
    {
        if ($model instanceof CreditFolderHeader) {
            return [
                'header_id' => $model->id,
                'detail_id' => null,
                'code' => $model->code,
                'source' => 'HEADER',
            ];
        }

        if ($model instanceof CreditFolderDetail) {
            $headerId = CreditFolderHeader::where('company_id', $model->company_id)
                ->where('code', $model->code_folder_header)
                ->value('id');

            return [
                'header_id' => $headerId,
                'detail_id' => $model->id,
                'code' => $model->code_folder_header,
                'source' => 'DETAIL',
            ];
        }

        throw new \InvalidArgumentException('Modelo no soportado para auditoria de creditos.');
    }

    private function resolveAction(Model $model, $event, $oldValues, $newValues)
    {
        if ($model instanceof CreditFolderHeader) {
            if ($event === 'CREATED') {
                return 'CREDITO_CREADO';
            }

            if ($event === 'DELETED') {
                return 'CREDITO_ELIMINADO';
            }

            if (array_key_exists('status', $newValues)) {
                return 'ESTADO_CREDITO_ACTUALIZADO';
            }

            return 'CREDITO_ACTUALIZADO';
        }

        if ($event === 'CREATED') {
            return 'CUOTA_CREADA';
        }

        if ($event === 'DELETED') {
            return 'CUOTA_ELIMINADA';
        }

        $oldStatus = array_key_exists('status', $oldValues) ? $oldValues['status'] : null;
        $newStatus = array_key_exists('status', $newValues) ? $newValues['status'] : null;

        if ($newStatus === 'PAGADA' && $oldStatus !== 'PAGADA') {
            return 'PAGO_REGISTRADO';
        }

        if ($oldStatus === 'PAGADA' && $newStatus !== null && $newStatus !== 'PAGADA') {
            return 'PAGO_ANULADO';
        }

        $paymentAttributes = [
            'valor_pagado',
            'date_pay',
            'hour_pay',
            'user_pay_id',
            'interes_mora',
            'numero_comprobante',
            'path',
        ];

        if (!empty(array_intersect($paymentAttributes, array_keys($newValues)))) {
            return 'PAGO_MODIFICADO';
        }

        return 'CUOTA_ACTUALIZADA';
    }

    private function description(Model $model, $action)
    {
        if ($model instanceof CreditFolderDetail) {
            return $action . ' - cuota ' . $model->numero_cuota;
        }

        return $action . ' - credito ' . $model->code;
    }

    private function withoutIgnoredAttributes(array $values)
    {
        return array_diff_key($values, array_flip(self::IGNORED_ATTRIBUTES));
    }

    private function userName($user)
    {
        if (!$user) {
            return null;
        }

        return trim($user->firstname . ' ' . $user->lastname) ?: $user->username;
    }

    private function requestValue($method)
    {
        if (app()->runningInConsole()) {
            return null;
        }

        return request()->{$method}();
    }
}
