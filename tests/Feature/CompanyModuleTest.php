<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Support\CompanyForm;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, View};
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CompanyModuleTest extends TestCase
{
    public function testLivewireSearchAndStatusUpdateWithoutNavigating()
    {
        $component = \Livewire\Livewire::test(\App\Http\Livewire\Company\CompanyIndex::class);
        $component->set('q', 'Segunda')->assertSee('Segunda')->assertDontSee('Primera');
        $component->call('confirmStatus', 2)->assertSet('pendingCompany', 2)->call('saveStatus')->assertHasNoErrors();
        $this->assertEquals(0, Company::find(2)->status);
        $component->set('estado', 'inactivas')->assertSee('Segunda')->call('confirmStatus', 2)->call('saveStatus');
        $this->assertEquals(1, Company::find(2)->status);
        $component->call('clearFilters')->assertSet('q', '')->assertSet('page', 1);
        $component->call('confirmStatus', 1)->call('saveStatus')->assertHasErrors('status');
        $this->assertEquals(1, Company::find(1)->status);
    }

    public function testLivewireEditorValidatesAndSavesAllSections()
    {
        $editor = \Livewire\Livewire::test(\App\Http\Livewire\Company\CompanyEditor::class, ['companyId' => 1]);
        $input = $this->input(); $input['contabilidad'] = true;
        $editor->set('data', $input)->call('save')->assertHasErrors('fecha_inicio_contable');
        $this->assertSame('Primera', Company::find(1)->company_name);
        $editor->set('data.fecha_inicio_contable', '2026-01-01')->set('data.mora', '3.50')->call('save')->assertHasNoErrors();
        $this->assertSame('CAJA ÁGUILA', Company::find(1)->company_name);
        $this->assertEquals(3.50, Company::find(1)->porcentaje_mora);
        $this->assertSame('Conservar', Company::find(1)->company_description);
        $editor->set('data.contabilidad', false)->call('save')->assertHasNoErrors();
        $this->assertNull(Company::find(1)->fecha_inicio_contable);
    }

    public function testLivewireCreationKeepsEditingTheSameCompanyAfterSave()
    {
        $editor = \Livewire\Livewire::test(\App\Http\Livewire\Company\CompanyEditor::class);
        $editor->set('data', $this->input())->call('save')->assertHasNoErrors();
        $editor->set('data.comercial_name', 'Modificada')->call('save')->assertHasNoErrors();
        $this->assertSame(3, Company::count());
        $this->assertSame('MODIFICADA', Company::orderByDesc('id')->first()->comercial_name);
    }

    public function testLivewireUploadsLogoToTheSharedWriter()
    {
        $root = storage_path('framework/testing/company-livewire-public');
        $this->app->instance('path.public', $root);
        \Illuminate\Support\Facades\Storage::fake('local');
        $editor = \Livewire\Livewire::test(\App\Http\Livewire\Company\CompanyEditor::class, ['companyId' => 1]);
        $editor->set('data', $this->input())->set('photo', UploadedFile::fake()->image('logo.png', 120, 80))->call('save')->assertHasNoErrors();
        $photo = Company::find(1)->photo;
        $path = $root . '/uploads/companies/' . $photo;
        try { $this->assertFileExists($path); }
        finally { if ($photo && is_file($path)) unlink($path); }
    }

    public function testLivewireCompanyActionsRejectGuestsAndExpiredSessions()
    {
        $component = new \App\Http\Livewire\Company\CompanyEditor();
        $component->mount(1);
        session(['last_activity' => time() - 1801]);
        try { $component->save(); $this->fail('Expected expired session rejection'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $error) { $this->assertSame(401, $error->getStatusCode()); }
        $this->assertGuest();
        $index = new \App\Http\Livewire\Company\CompanyIndex();
        try { $index->confirmStatus(2); $this->fail('Expected guest rejection'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $error) { $this->assertSame(401, $error->getStatusCode()); }
        $this->assertEquals(1, Company::find(2)->status);
    }

    public function testLivewireEditorRejectsATamperedRecordToken()
    {
        $component = new \App\Http\Livewire\Company\CompanyEditor();
        $component->mount(1); $component->record = '2';
        try { $component->save(); $this->fail('Expected invalid record rejection'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $error) { $this->assertSame(403, $error->getStatusCode()); }
        $this->assertSame('Segunda', Company::find(2)->company_name);
    }
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'company_test', 'database.connections.company_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('company', function (Blueprint $table) {
            $table->increments('id'); $table->boolean('status')->default(true);
            $table->string('photo')->nullable(); $table->string('company_description')->nullable();
            foreach (CompanyForm::fields() as $name => $field) $table->string($field[5] ?? $name)->nullable();
            $table->timestamps();
        });
        Schema::create('category', function (Blueprint $table) {
            $table->increments('id'); $table->string('title'); $table->integer('type'); $table->boolean('status');
        });
        $user = new User(); $user->id = 1; $user->company_id = 1; $user->firstname = 'Admin';
        $this->actingAs($user);
        $this->app['events']->forget('composing: layouts.app');
        View::share('menusComposer', []);
        foreach ([1 => 'Primera', 2 => 'Segunda'] as $id => $name) DB::table('company')->insert([
            'id' => $id, 'company_name' => $name, 'comercial_name' => $name, 'ruc' => '0123456789001',
            'company_type' => 1, 'status' => true, 'contabilidad' => false, 'company_description' => 'Conservar',
        ]);
    }

    private function input()
    {
        $input = [];
        foreach (CompanyForm::fields() as $key => $field) $input[$key] = $field[3] ?? '';
        return array_merge($input, ['company_name' => 'Caja Águila', 'comercial_name' => 'Águila', 'ruc' => '0123456789001']);
    }

    public function testListFiltersAndBothFormsRenderWithoutDuplicateFieldIds()
    {
        $this->get('/company?q=Segunda')->assertOk()->assertSee('Segunda')->assertDontSee('Primera');
        DB::table('company')->where('id', 2)->update(['status' => false]);
        $this->get('/company')->assertOk()->assertSee('Primera')->assertDontSee('Segunda');
        $this->get('/company?estado=inactivas')->assertOk()->assertSee('Segunda')->assertDontSee('Primera');
        foreach (['/company/create', '/company/1/edit'] as $url) {
            $html = $this->get($url)->assertOk()->assertSee('Contabilidad y operación')->assertSee('Documentos y presentación')->getContent();
            $this->assertSame(1, substr_count($html, 'id="company_name"'));
            $this->assertSame(1, substr_count($html, 'id="ruc"'));
        }
    }

    public function testSaveUsesCorrectSwitchesAndPreservesSettingsOutsideTheForm()
    {
        $input = $this->input(); $input['conexion'] = 1; $input['electronica'] = 0;
        $this->post('/company', $input)->assertRedirect('/company')->assertSessionHasNoErrors();
        $created = Company::orderByDesc('id')->first();
        $this->assertSame('CAJA ÁGUILA', $created->company_name);
        $this->assertEquals(1, $created->conexion);
        $this->assertEquals(0, $created->electronica);
        $this->assertNull($created->category_type);
        $input['conexion'] = 0; $input['electronica'] = 1; $input['mora'] = '2.50';
        $this->put('/company/1', $input)->assertRedirect('/company/1/edit')->assertSessionHasNoErrors();
        $updated = Company::find(1);
        $this->assertSame('Conservar', $updated->company_description);
        $this->assertEquals(0, $updated->conexion);
        $this->assertEquals(1, $updated->electronica);
        $this->assertEquals(2.50, $updated->porcentaje_mora);
    }

    public function testInvalidFinancialSettingsAndMissingConditionalDatesDoNotSave()
    {
        $input = $this->input();
        $input['contabilidad'] = 1; $input['active_cron'] = 1; $input['mora'] = -1;
        $this->from('/company/1/edit')->put('/company/1', $input)->assertSessionHasErrors(['fecha_inicio_contable', 'time_cron', 'mora'])->assertSessionHasInput('comercial_name', 'Águila');
        $this->assertSame('Primera', Company::find(1)->company_name);
        $input['fecha_inicio_contable'] = '2026-01-01'; $input['time_cron'] = '08:30'; $input['mora'] = 1;
        $this->put('/company/1', $input)->assertSessionHasNoErrors();
        $this->assertSame('2026-01-01', Company::find(1)->fecha_inicio_contable);
    }

    public function testStatusIsReversibleAndCannotDisableTheCurrentCompany()
    {
        $this->patch('/company/1/estado', ['status' => 0])->assertSessionHasErrors('status');
        $this->patch('/company/2/estado', ['status' => 0])->assertSessionHasNoErrors();
        $this->assertEquals(0, Company::find(2)->status);
        $this->patch('/company/2/estado', ['status' => 1])->assertSessionHasNoErrors();
        $this->assertEquals(1, Company::find(2)->status);
        $this->assertSame(2, Company::count());
    }

    public function testInvalidLogoIsRejectedAndExistingLogoIsPreservedWithoutUpload()
    {
        DB::table('company')->where('id', 1)->update(['photo' => 'original.png']);
        $input = $this->input(); $input['photo'] = UploadedFile::fake()->create('malicious.php', 1, 'text/plain');
        $this->put('/company/1', $input)->assertSessionHasErrors('photo');
        $this->assertSame('original.png', Company::find(1)->photo);
        unset($input['photo']);
        $this->put('/company/1', $input)->assertSessionHasNoErrors();
        $this->assertSame('original.png', Company::find(1)->photo);
    }

    public function testLogoUploadUsesANewFilenameInAnIsolatedDirectory()
    {
        $root = storage_path('framework/testing/company-public');
        $this->app->instance('path.public', $root);
        $input = $this->input(); $input['photo'] = UploadedFile::fake()->image('logo.png', 120, 80);
        $this->put('/company/1', $input)->assertSessionHasNoErrors();
        $filename = Company::find(1)->photo;
        $path = $root . '/uploads/companies/' . $filename;
        try {
            $this->assertNotSame('logo.png', $filename);
            $this->assertFileExists($path);
        } finally {
            if ($filename && is_file($path)) unlink($path);
        }
    }
}
