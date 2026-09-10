<?php

namespace Tests\Feature;

use App\Http\Livewire\TipoAhorros\TipoAhorrosComponent;
use App\Support\SavingsPalette;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SavingsPaletteTest extends TestCase
{
    public function testCustomColorsAreResolvedWithoutLosingTheChosenHue()
    {
        $this->assertSame('#ab12ef', SavingsPalette::hex('#AB12EF'));
        $this->assertStringContainsString('--account-accent:#ab12ef;', SavingsPalette::style('#AB12EF'));
        $this->assertStringContainsString('--account-background:#ffffff;', SavingsPalette::style('#ffffff'));
        $this->assertStringContainsString('--account-background:#e0e0e0;', SavingsPalette::style('#000000'));
        $this->assertSame('#087e9a', SavingsPalette::hex('info'));
        $this->assertStringContainsString('background-color:var(--account-background)', SavingsPalette::legacyStyle('#ab12ef'));
    }

    public function testSavingRejectsColorsOutsideThePaletteBeforeWriting()
    {
        $component = new TipoAhorrosComponent();
        $component->name = 'Ahorro';
        $component->edad_min = 18;
        $component->edad_max = 90;
        $component->interes = 2;
        $component->class = 'red; background: url(example)';

        try {
            $component->store();
            $this->fail('An unsupported color must not be saved.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('class', $exception->errors());
        }
    }

    public function testLegacyColorsRemainDistinctAndMissingColorsHaveASafeFallback()
    {
        $styles = [];
        foreach (['info', 'success', 'warning', 'danger'] as $legacyColor) {
            $this->assertArrayHasKey($legacyColor, SavingsPalette::colors());
            $styles[] = SavingsPalette::style($legacyColor);
        }
        $this->assertCount(4, array_unique($styles));
        $this->assertSame(SavingsPalette::style('info'), SavingsPalette::style(null));
        $this->assertSame(SavingsPalette::style('info'), SavingsPalette::style('invalid; color:red'));
    }
}
