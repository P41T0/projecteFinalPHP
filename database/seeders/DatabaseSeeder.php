<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory(10)->create();

            DB::table('users')->insert(['name' => 'Pau', 'email' => 'pau.tort@uvic.cat', 'password' => bcrypt('123456'), 'admin' => TRUE]);
            DB::table('seccio')->insert(['nom'=>'Telefonia mòbil','nom_es'=>'Telefonia mòbil','nom_en'=>'Telefonia mòbil','descripcio'=>'telèfons mòbils, smartwatches, auriculars i altres accessoris','descripcio_es'=>'telèfons mòbils, smartwatches, auriculars i altres accessoris','descripcio_en'=>'telèfons mòbils, smartwatches, auriculars i altres accessoris','mostra_sec'=>TRUE,]);
            DB::table('seccio')->insert(['nom'=>'Ordinadors','nom_es'=>'Ordinadors','nom_en'=>'Ordinadors','descripcio'=>'Ordinadors portàtils i accessoris','descripcio_es'=>'Ordinadors portàtils i accessoris','descripcio_en'=>'Ordinadors portàtils i accessoris','mostra_sec'=>TRUE,]);
            DB::table('producte')->insert(['nom'=>'IPhone 15','nom_es'=>'IPhone 15','nom_en'=>'IPhone 15','descripcio'=>'telefon mòbil','descripcio_es'=>'telefon mòbil','descripcio_en'=>'telefon mòbil','foto'=>'iPhone15_black.webp', 'preu_unitari'=>'1499.99','mostra_prod'=>TRUE,'seccio_id'=>'1']);
            DB::table('producte')->insert(['nom'=>'Google Pixel 8','nom_es'=>'Google Pixel 8','nom_en'=>'Google Pixel 8','descripcio'=>'telefon mòbil','descripcio_es'=>'telefon mòbil','descripcio_en'=>'telefon mòbil','foto'=>'google_pixel_8_5g_rosa_Frontback.webp', 'preu_unitari'=>'899.99','mostra_prod'=>TRUE,'seccio_id'=>'1']);
            DB::table('producte')->insert(['nom'=>'ASUS TUF GAMING','nom_es'=>'ASUS TUF GAMING','nom_en'=>'ASUS TUF GAMING','descripcio'=>'ordinador portàtil','descripcio_es'=>'ordinador portàtil','descripcio_en'=>'ordinador portàtil','foto'=>'audio_pd.png', 'preu_unitari'=>'1299.99','mostra_prod'=>TRUE,'seccio_id'=>'2']);
            DB::table('producte')->insert(['nom'=>'Chromebook','nom_es'=>'Chromebook','nom_en'=>'Chromebook','descripcio'=>'ordinador portàtil','descripcio_es'=>'ordinador portàtil','descripcio_en'=>'ordinador portàtil','foto'=>'lenovo-laptop-lenovo-500e-gen3-hero.webp', 'preu_unitari'=>'399.99','mostra_prod'=>TRUE,'seccio_id'=>'2']);
            DB::table('botiga')->insert(['poblacio'=>'Roda de Ter','adreca'=>'Carrer Nou, nº 25','telefon'=>'938256145','correu'=>'botigaroda@botiga.cat']);
            DB::table('botiga')->insert(['poblacio'=>'Vic','adreca'=>'Plaça Major, nº 10','telefon'=>'938542662','correu'=>'botigavic@botiga.cat']);
            DB::table('botiga')->insert(['poblacio'=>'Manlleu','adreca'=>'Plaça Fra Bernardí, nº 1','telefon'=>'938234512','correu'=>'botigamanlleu@botiga.cat']);
            DB::table('comanda')->insert(['botiga_id'=>'1','usuari_id'=>'1']);
            DB::table('comanda')->insert(['botiga_id'=>'2','usuari_id'=>'3']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'1','producte_id'=>'1','quantitat'=>'3']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'2','producte_id'=>'1','quantitat'=>'8']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'3','producte_id'=>'1','quantitat'=>'0']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'1','producte_id'=>'3','quantitat'=>'5']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'2','producte_id'=>'3','quantitat'=>'0']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'3','producte_id'=>'3','quantitat'=>'0']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'1','producte_id'=>'2','quantitat'=>'0']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'2','producte_id'=>'2','quantitat'=>'0']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'3','producte_id'=>'2','quantitat'=>'4']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'1','producte_id'=>'4','quantitat'=>'0']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'2','producte_id'=>'4','quantitat'=>'6']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'3','producte_id'=>'4','quantitat'=>'0']);
            DB::table('linia_comanda')->insert(['comanda_id'=>'1','producte_id'=>'1','quantitat'=>'4']);
            DB::table('linia_comanda')->insert(['comanda_id'=>'1','producte_id'=>'3','quantitat'=>'6']);
            DB::table('linia_comanda')->insert(['comanda_id'=>'2','producte_id'=>'1','quantitat'=>'2']);
            DB::table('linia_comanda')->insert(['comanda_id'=>'2','producte_id'=>'4','quantitat'=>'8']);
    }
}
