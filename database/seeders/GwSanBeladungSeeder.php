<?php

namespace Database\Seeders;

use App\Models\EquipmentInventory;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\VehicleEquipment;
use Illuminate\Database\Seeder;

class GwSanBeladungSeeder extends Seeder
{
    private const VEHICLE_ID = 1;

    public function run(): void
    {
        // ── Kategorien ────────────────────────────────────────────────────────
        $cats = [];
        $catDefs = [
            'Rettungs- & Transportmittel' => ['color' => '#7C3AED'],
            'Infrastruktur'               => ['color' => '#1E40AF'],
            'Logistik & Sicherheit'       => ['color' => '#92400E'],
            'Elektrisches Zubehör'        => ['color' => '#D97706'],
            'Fahrzeug-Verbrauchsmaterial' => ['color' => '#059669'],
        ];
        foreach ($catDefs as $name => $attrs) {
            $cats[$name] = MaterialCategory::firstOrCreate(
                ['name' => $name],
                ['color' => $attrs['color'], 'description' => '']
            );
        }

        // ── Hilfsfunktion: Material + EquipmentInventory anlegen ─────────────
        $add = function (
            int              $equipmentId,
            string           $name,
            string           $categoryKey,
            string           $unit,
            int              $qty,
            ?string          $articleNumber = null,
            bool             $isDevice      = false,
        ) use (&$cats): void {
            $material = Material::firstOrCreate(
                ['name' => $name],
                [
                    'article_number' => $articleNumber,
                    'category_id'    => $cats[$categoryKey]->id,
                    'unit'           => $unit,
                    'min_stock'      => 0,
                    'is_active'      => true,
                    'is_device'      => $isDevice,
                    'has_expiry'     => false,
                ]
            );

            EquipmentInventory::firstOrCreate(
                ['equipment_id' => $equipmentId, 'material_id' => $material->id],
                ['target_quantity' => $qty, 'current_quantity' => $qty]
            );
        };

        // ═══════════════════════════════════════════════════════════════════
        // BLOCK 1 – Rettungsmittel & Tragen (Einzelverlastung, S-Codes)
        // ═══════════════════════════════════════════════════════════════════
        $equip1 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Rettungsmittel & Tragen'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => 'Verlastete Rettungs- und Transportmittel GW-San']
        );

        $add($equip1->id, 'AED Plus mit EKG-Anzeige',             'Rettungs- & Transportmittel', 'Stück', 1,  'S02', true);
        $add($equip1->id, 'Cervicalstütze Erwachsene (4-fach)',   'Rettungs- & Transportmittel', 'Stück', 10, 'S21');
        $add($equip1->id, 'Cervicalstütze Kinder (3-fach)',       'Rettungs- & Transportmittel', 'Stück', 10, 'S22');
        $add($equip1->id, 'Krankentrage DIN 13024-2 klappbar',    'Rettungs- & Transportmittel', 'Stück', 10, 'S10');
        $add($equip1->id, 'Infusionsständer für Krankentrage',    'Rettungs- & Transportmittel', 'Stück', 10, 'S19');
        $add($equip1->id, 'Krankentragenlagerungsbock (faltbar)', 'Rettungs- & Transportmittel', 'Stück', 3,  'S09');
        $add($equip1->id, 'Spineboard mit Gurtsystem',            'Rettungs- & Transportmittel', 'Stück', 5,  'S12');
        $add($equip1->id, 'Schaufeltrage EN 1865',                'Rettungs- & Transportmittel', 'Stück', 2,  'S11');
        $add($equip1->id, 'Vakuum-Matratze EN 1865',              'Rettungs- & Transportmittel', 'Satz',  1,  'S20');
        $add($equip1->id, 'Vakuum-Schienen-Set (Arm/Unterarm/Bein)', 'Rettungs- & Transportmittel', 'Stück', 1, 'S23');

        // ═══════════════════════════════════════════════════════════════════
        // BLOCK 2 – Unterkunft & Logistik
        // ═══════════════════════════════════════════════════════════════════
        $equip2 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Unterkunft & Logistik'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => 'Infrastruktur, Möbel, Logistik GW-San']
        );

        $add($equip2->id, 'Dauerdruckzelt 34 m²',                   'Infrastruktur',         'Stück', 1,  'S06');
        $add($equip2->id, 'Zeltheizung 25 kW (dieselbetrieben)',     'Infrastruktur',         'Stück', 1,  'S07');
        $add($equip2->id, 'Drehstrom-/Wechselstromerzeuger 6 kVA',  'Infrastruktur',         'Stück', 1,  'S08', true);
        $add($equip2->id, 'Kraftstoffkanister 20 l (Stahl)',         'Logistik & Sicherheit', 'Stück', 2,  'S14');
        $add($equip2->id, 'Kabeltrommel 230V 50m 3×2,5mm²',         'Elektrisches Zubehör',  'Stück', 2,  'S15');
        $add($equip2->id, 'Teleskop-Dreibeinstativ',                 'Logistik & Sicherheit', 'Stück', 3,  'S03');
        $add($equip2->id, 'Klapptisch 2200×500 mm',                 'Logistik & Sicherheit', 'Stück', 1,  'S16');
        $add($equip2->id, 'Klappstuhl Hartholz',                    'Logistik & Sicherheit', 'Stück', 6,  'S17');
        $add($equip2->id, 'Abfallsammler für Säcke bis 120 l',      'Logistik & Sicherheit', 'Stück', 2,  'S04');
        $add($equip2->id, 'Abfallsack 120 l PE',                    'Logistik & Sicherheit', 'Stück', 20, 'S05');
        $add($equip2->id, 'Wasserkanister 20 l mit Auslaufhahn',    'Logistik & Sicherheit', 'Stück', 1,  'S13');
        $add($equip2->id, 'Mehrzweck-Pulverlöscher 6 kg',           'Logistik & Sicherheit', 'Stück', 1,  'S18');
        $add($equip2->id, 'Steh- und Anlegeleiter 4-teilig',        'Logistik & Sicherheit', 'Stück', 1,  'S24');
        $add($equip2->id, 'Faltsignale-Set (4 Stück)',              'Logistik & Sicherheit', 'Satz',  1,  'S25');
        $add($equip2->id, 'Dokumententasche (Bedienungsanleitungen)', 'Logistik & Sicherheit', 'Stück', 1);

        // ═══════════════════════════════════════════════════════════════════
        // ZARGES-BOXEN
        // ═══════════════════════════════════════════════════════════════════

        // Box SA1 – Hygiene (600×400×340)
        $sa1 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SA1 – Hygiene'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '600×400×340 mm – Hygiene & Desinfektion']
        );
        $add($sa1->id, 'Hygienemodul mit Spendern (Seife/Desinfektion/Handtücher)', 'Logistik & Sicherheit',      'Satz',  1);
        $add($sa1->id, 'Verbrauchsmaterial Hygiene (Seife, Desinfektion, Tücher)',  'Fahrzeug-Verbrauchsmaterial', 'Stück', 20);

        // Box SB2 – Medizinisches Verbrauchsmaterial (800×600×410)
        $sb2 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SB2 – Med. Verbrauch'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '800×600×410 mm – Medizinisches Verbrauchsmaterial']
        );
        $add($sb2->id, 'Aluminium-Formschienen-Set',                     'Rettungs- & Transportmittel', 'Satz',  10);
        $add($sb2->id, 'Untersuchungshandschuhe (100er Spenderpackung)', 'Fahrzeug-Verbrauchsmaterial', 'Pack',  3);
        $add($sb2->id, 'Tasche für Patientenanhängekarten (50 Karten)',  'Logistik & Sicherheit',       'Stück', 3);
        $add($sb2->id, 'Gurtenstrap für Spineboard',                     'Rettungs- & Transportmittel', 'Stück', 5);
        $add($sb2->id, 'Kopffixierung für Spineboard',                   'Rettungs- & Transportmittel', 'Stück', 5);
        $add($sb2->id, 'Kanülenabwurf 2,0 l',                           'Fahrzeug-Verbrauchsmaterial', 'Stück', 1);

        // Box SB3 – Elektro (800×600×410)
        $sb3 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SB3 – Elektro'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '800×600×410 mm – Elektrisches Zubehör']
        );
        $add($sb3->id, 'Flutlichtstrahler 230V/1000W',          'Elektrisches Zubehör', 'Stück', 1);
        $add($sb3->id, 'Mehrfachabzweigstück druckwasserdicht', 'Elektrisches Zubehör', 'Stück', 1);
        $add($sb3->id, 'Verlängerungskabel 230V 10m 3×2,5mm²', 'Elektrisches Zubehör', 'Stück', 2);
        $add($sb3->id, 'Zeltleuchte 230V 2×55W',                'Elektrisches Zubehör', 'Stück', 3);

        // Box SC1 – Rettungsdecken (600×400×250)
        $sc1 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SC1 – Rettungsdecken'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '600×400×250 mm – Einweg-Rettungstücher 2000×700 mm']
        );
        $add($sc1->id, 'Einweg-Rettungstuch Gittergewebe 2000×700 mm', 'Fahrzeug-Verbrauchsmaterial', 'Stück', 20);

        // Box SC2 – Druckgas/Zelt (600×400×250)
        $sc2 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SC2 – Druckgas'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '600×400×250 mm – Zeltgebläse & Druckminderer']
        );
        $add($sc2->id, 'Zeltgebläse mit Schläuchen',           'Infrastruktur', 'Stück', 1);
        $add($sc2->id, 'Druckminderer 300bar mit Füllschläuchen', 'Infrastruktur', 'Stück', 1);

        // Box SD2 – Einmaldecken (600×400×410)
        $sd2 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SD2 – Einmaldecken'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '600×400×410 mm – Einmaldecken 1900×1100 mm']
        );
        $add($sd2->id, 'Einmaldecke mehrlagig Zellstoff 1900×1100 mm', 'Fahrzeug-Verbrauchsmaterial', 'Stück', 20);

        // Box SD3 – Erste-Hilfe-Packs (600×400×410)
        $sd3 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SD3 – EH-Packs'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '600×400×410 mm – Erste-Hilfe-Packs']
        );
        $add($sd3->id, 'Erste-Hilfe-Pack (Infusionsset, Pflaster, Kompressen, Mullbinde, Rettungsdecke, Verbandmaterial)', 'Fahrzeug-Verbrauchsmaterial', 'Stück', 25);

        // Box SD4 – Gurtzeug (600×400×410)
        $sd4 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SD4 – Gurtzeug'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '600×400×410 mm – Tragegurte, Ausgussstutzen, Abspannbänder']
        );
        $add($sd4->id, 'Tragegurt DIN 13044',                                  'Rettungs- & Transportmittel', 'Stück', 20);
        $add($sd4->id, 'Ausguss-Stutzen für Kraftstoffkanister (kraftstofffest)', 'Logistik & Sicherheit',     'Stück', 2);
        $add($sd4->id, 'Abspannband rot/weiß 80mm 500m',                       'Logistik & Sicherheit',       'Stück', 2);

        // Box SE1 – O2-Flaschen (400×300×470, 2 Boxen à 5 Stück)
        $se1 = VehicleEquipment::firstOrCreate(
            ['vehicle_id' => self::VEHICLE_ID, 'name' => 'Zarges Box SE1 – O2-Flaschen'],
            ['type' => 'sonstige', 'is_active' => true, 'description' => '400×300×470 mm – 2 Boxen à 5 Sauerstoffflaschen 2,0 l Aluminium']
        );
        $add($se1->id, 'Sauerstofflasche 2,0 l Aluminium (Box SE1)', 'Rettungs- & Transportmittel', 'Stück', 10, null, true);

        $this->command->info('GW-San Beladeliste erfolgreich importiert.');
    }
}
