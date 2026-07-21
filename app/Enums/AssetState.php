<?php

namespace App\Enums;

use Dpb\Package\Assets\Contracts\AssetStateInterface;
use Dpb\Package\Assets\Contracts\TransitionValidatorInterface;
use Illuminate\Database\Eloquent\Model;

enum AssetState: string implements AssetStateInterface
{
    case NA_VYRADENIE_NESCHVALENE = 'na_vyradenie_neschvalene';
    case VYRADENE_SCHVALENE = 'vyradene_schvalene';
    case VYZISK = 'vyzisk';
    case NA_OPRAVU_NEZARUCNA = 'na_opravu_nezarucna';
    case NA_OPRAVU_ZARUCNA = 'na_opravu_zarucna';
    case NA_OPRAVU_REKLAMACIA = 'na_opravu_reklamacia';
    case PRIJEM_Z_DIELNE = 'prijem_z_dielne';
    case VYDAJ_NA_OPRAVU_NEZARUCNA = 'vydaj_na_opravu_nezarucna';
    case VYDAJ_NA_OPRAVU_REKLAMACIA = 'vydaj_na_opravu_reklamacia';
    case VYDAJ_NA_OPRAVU_ZARUCNA = 'vydaj_na_opravu_zarucna';
    case OBJ_OPRAVA_VYTVORENIE = 'obj_oprava_vytvorenie';
    case OBJ_OPRAVA_SCHVALENIE = 'obj_oprava_schvalenie';
    case OBJ_OPRAVA_ODOSLANIE = 'obj_oprava_odoslanie';
    case PRIJEM_Z_OPRAVY_ZARUCNA = 'prijem_z_opravy_zarucna';
    case PRIJEM_Z_OPRAVY_NEZARUCNA = 'prijem_z_opravy_nezarucna';
    case PRIJEM_Z_REKLAMACIE = 'prijem_z_reklamacie';
    case VYDAJ_NA_MONTAZ = 'vydaj_na_montaz';
    case DIEL_NA_VOZE = 'diel_na_voze';
    case PNEU_POGUMOVANIE_1 = 'pneu_pogumovanie_1';
    case PNEU_POGUMOVANIE_2 = 'pneu_pogumovanie_2';
    case VYTVORENY_NA_PODZAKAZKE = 'vytvoreny_na_podzakazke';

    public function value(): string
    {
        return $this->value;
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::DIEL_NA_VOZE => [self::NA_VYRADENIE_NESCHVALENE, self::VYZISK, self::NA_OPRAVU_NEZARUCNA, self::NA_OPRAVU_ZARUCNA, self::NA_OPRAVU_REKLAMACIA],
            self::NA_VYRADENIE_NESCHVALENE => [self::VYRADENE_SCHVALENE],
            self::VYZISK => [self::DIEL_NA_VOZE],
            self::NA_OPRAVU_NEZARUCNA => [self::DIEL_NA_VOZE, self::VYZISK, self::PRIJEM_Z_DIELNE],
            self::NA_OPRAVU_ZARUCNA => [self::DIEL_NA_VOZE, self::VYZISK, self::PRIJEM_Z_DIELNE],
            self::NA_OPRAVU_REKLAMACIA => [self::DIEL_NA_VOZE, self::VYZISK, self::PRIJEM_Z_DIELNE],
            self::PRIJEM_Z_DIELNE => [self::VYDAJ_NA_OPRAVU_NEZARUCNA, self::VYDAJ_NA_OPRAVU_ZARUCNA, self::VYDAJ_NA_OPRAVU_REKLAMACIA],
            self::VYDAJ_NA_OPRAVU_NEZARUCNA => [self::PRIJEM_Z_OPRAVY_NEZARUCNA],
            self::VYDAJ_NA_OPRAVU_ZARUCNA => [self::PRIJEM_Z_OPRAVY_ZARUCNA],
            self::VYDAJ_NA_OPRAVU_REKLAMACIA => [self::PRIJEM_Z_REKLAMACIE],
            self::PRIJEM_Z_OPRAVY_ZARUCNA => [self::VYDAJ_NA_MONTAZ],
            self::PRIJEM_Z_OPRAVY_NEZARUCNA => [self::VYDAJ_NA_MONTAZ],
            self::PRIJEM_Z_REKLAMACIE => [self::VYDAJ_NA_MONTAZ],
            self::VYDAJ_NA_MONTAZ => [self::DIEL_NA_VOZE],
            self::VYTVORENY_NA_PODZAKAZKE => [self::DIEL_NA_VOZE],
            self::VYRADENE_SCHVALENE => [], // Terminal state
            default => [],
        };
    }

    public function canTransitionTo(AssetStateInterface $targetState, Model $asset): bool
    {
        // 1. Structural check (Static State Diagram Verification)
        if (! in_array($targetState, $this->allowedTransitions(), true)) {
            return false;
        }

        // 2. Resolve the App-Specific Business Logic Validator from the Service Container
        if (app()->bound(TransitionValidatorInterface::class)) {
            return app(TransitionValidatorInterface::class)->validate($asset, $this, $targetState);
        }

        return true;
    }

    public function label(): string
    {
        return match ($this) {
            self::NA_VYRADENIE_NESCHVALENE => 'Na vyradenie (neschválené)',
            self::VYRADENE_SCHVALENE => 'Vyradené (schválené)',
            self::VYZISK => 'Výzisk',
            self::NA_OPRAVU_NEZARUCNA => 'Na opravu - nezáručná',
            self::NA_OPRAVU_ZARUCNA => 'Na opravu - záručná',
            self::NA_OPRAVU_REKLAMACIA => 'Na opravu - reklamácia',
            self::PRIJEM_Z_DIELNE => 'Príjem z dielne na MTZ',
            self::VYDAJ_NA_OPRAVU_NEZARUCNA => 'Výdaj na opravu - nezáručná',
            self::VYDAJ_NA_OPRAVU_REKLAMACIA => 'Výdaj na opravu - reklamácia',
            self::VYDAJ_NA_OPRAVU_ZARUCNA => 'Výdaj na opravu - záručná',
            self::OBJ_OPRAVA_VYTVORENIE => 'Objednávka na opravu - vytvorenie',
            self::OBJ_OPRAVA_SCHVALENIE => 'Objednávka na opravu - schválenie',
            self::OBJ_OPRAVA_ODOSLANIE => 'Objednávka na opravu - odoslanie',
            self::PRIJEM_Z_OPRAVY_ZARUCNA => 'Príjem z opravy - záručná',
            self::PRIJEM_Z_OPRAVY_NEZARUCNA => 'Príjem z opravy - nezáručná',
            self::PRIJEM_Z_REKLAMACIE => 'Príjem z reklamácie',
            self::VYDAJ_NA_MONTAZ => 'Výdaj na montáž',
            self::DIEL_NA_VOZE => 'Diel na voze',
            self::PNEU_POGUMOVANIE_1 => 'Odosielanie na 1. pogumovanie',
            self::PNEU_POGUMOVANIE_2 => 'Odosielanie na 2. pogumovanie',
            self::VYTVORENY_NA_PODZAKAZKE => 'Vytvorený na podzákazke',
        };
    }

    public function location(): string
    {
        return match ($this) {
            self::NA_VYRADENIE_NESCHVALENE,
            self::VYRADENE_SCHVALENE,
            self::VYZISK,
            self::NA_OPRAVU_NEZARUCNA,
            self::NA_OPRAVU_ZARUCNA,
            self::NA_OPRAVU_REKLAMACIA,
            self::VYDAJ_NA_MONTAZ => 'prevádzka',

            self::PRIJEM_Z_DIELNE,
            self::OBJ_OPRAVA_VYTVORENIE,
            self::OBJ_OPRAVA_SCHVALENIE,
            self::OBJ_OPRAVA_ODOSLANIE,
            self::PRIJEM_Z_OPRAVY_ZARUCNA,
            self::PRIJEM_Z_OPRAVY_NEZARUCNA,
            self::PRIJEM_Z_REKLAMACIE => 'sklad MTZ',

            self::VYDAJ_NA_OPRAVU_NEZARUCNA,
            self::VYDAJ_NA_OPRAVU_REKLAMACIA,
            self::VYDAJ_NA_OPRAVU_ZARUCNA => 'v oprave',

            self::DIEL_NA_VOZE => 'vozidlo',

            self::PNEU_POGUMOVANIE_1,
            self::PNEU_POGUMOVANIE_2 => 'gumári',

            self::VYTVORENY_NA_PODZAKAZKE => 'neexistuje',
        };
    }

    public function isFunctional(): bool
    {
        return match ($this) {
            self::VYZISK,
            self::VYDAJ_NA_MONTAZ,
            self::DIEL_NA_VOZE,
            self::VYTVORENY_NA_PODZAKAZKE => true,

            default => false,
        };
    }
}
