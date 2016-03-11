<?php

use Illuminate\Database\Seeder;


class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('languages')->insert(
            array(
                array('description' => '---'),
                array('description' => 'abq'),
                array('description' => 'av'),
                array('description' => 'en'),
                array('description' => 'ady'),
                array('description' => 'az'),
                array('description' => 'ht'),
                array('description' => 'ak'),
                array('description' => 'sq'),
                array('description' => 'ale'),
                array('description' => 'am'),
                array('description' => 'ab'),
                array('description' => 'hsb'),
                array('description' => 'ar'),
                array('description' => 'an'),
                array('description' => 'hy'),
                array('description' => 'aae'),
                array('description' => 'grc'),
                array('description' => 'as'),
                array('description' => 'mey'),
                array('description' => 'ast'),
                array('description' => 'ay'),
                array('description' => 'aa'),
                array('description' => 'af'),
                array('description' => 'wa'),
                array('description' => 'bal'),
                array('description' => 'bm'),
                array('description' => 'bcj'),
                array('description' => 'eu'),
                array('description' => 'ba'),
                array('description' => 'bar'),
                array('description' => 'bn'),
                array('description' => 'vec'),
                array('description' => 've'),
                array('description' => 'vi'),
                array('description' => 'my'),
                array('description' => 'bi'),
                array('description' => 'bh'),
                array('description' => 'nd'),
                array('description' => 'vo'),
                array('description' => 'sme'),
                array('description' => 'bs'),
                array('description' => 'pcc'),
                array('description' => 'bg'),
                array('description' => 'br'),
                array('description' => 'fr'),
                array('description' => 'frp'),
                array('description' => 'de'),
                array('description' => 'ka'),
                array('description' => 'yi'),
                array('description' => 'yo'),
                array('description' => 'esu'),
                array('description' => 'guz'),
                array('description' => 'dag'),
                array('description' => 'da'),
                array('description' => 'he'),
                array('description' => 'gsw'),
                array('description' => 'el'),
                array('description' => 'egl'),
                array('description' => 'myv'),
                array('description' => 'et'),
                array('description' => 'eo'),
                array('description' => 'zza'),
                array('description' => 'za'),
                array('description' => 'zu'),
                array('description' => 'bo'),
                array('description' => 'jv'),
                array('description' => 'ja'),
                array('description' => 'ig'),
                array('description' => 'ibd'),
                array('description' => 'yey'),
                array('description' => 'inh'),
                array('description' => 'hi'),
                array('description' => 'ind'),
                array('description' => 'iu'),
                array('description' => 'ik'),
                array('description' => 'ia'),
                array('description' => 'ga'),
                array('description' => 'is'),
                array('description' => 'es'),
                array('description' => 'lad'),
                array('description' => 'it'),
                array('description' => 'kbd'),
                array('description' => 'kk'),
                array('description' => 'kl'),
                array('description' => 'xal'),
                array('description' => 'sro'),
                array('description' => 'kab'),
                array('description' => 'kn'),
                array('description' => 'kr'),
                array('description' => 'yue'),
                array('description' => 'ks'),
                array('description' => 'csb'),
                array('description' => 'ca'),
                array('description' => 'zpf'),
                array('description' => 'rw'),
                array('description' => 'bcl'),
                array('description' => 'qu'),
                array('description' => 'ki'),
                array('description' => 'kmb'),
                array('description' => 'zh'),
                array('description' => 'cjy'),
                array('description' => 'wuu'),
                array('description' => 'hsn'),
                array('description' => 'gan'),
                array('description' => 'rn'),
                array('description' => 'tlh'),
                array('description' => 'kv'),
                array('description' => 'kg'),
                array('description' => 'gom'),
                array('description' => 'ko'),
                array('description' => 'kw'),
                array('description' => 'co'),
                array('description' => 'avk'),
                array('description' => 'kj'),
                array('description' => 'ku'),
                array('description' => 'kmr'),
                array('description' => 'kfr'),
                array('description' => 'kea'),
                array('description' => 'lou'),
                array('description' => 'cr'),
                array('description' => 'mus'),
                array('description' => 'kri'),
                array('description' => 'hr'),
                array('description' => 'ky'),
                array('description' => 'lzz'),
                array('description' => 'lbe'),
                array('description' => 'lkt'),
                array('description' => 'lo'),
                array('description' => 'ltg'),
                array('description' => 'la'),
                array('description' => 'lez'),
                array('description' => 'lv'),
                array('description' => 'be'),
                array('description' => 'lij'),
                array('description' => 'lt'),
                array('description' => 'li'),
                array('description' => 'loz'),
                array('description' => 'ilo'),
                array('description' => 'lmo'),
                array('description' => 'lb'),
                array('description' => 'lrc'),
                array('description' => 'mg'),
                array('description' => 'mzn'),
                array('description' => 'vmf'),
                array('description' => 'ml'),
                array('description' => 'ms'),
                array('description' => 'mt'),
                array('description' => 'gv'),
                array('description' => 'mnc'),
                array('description' => 'arn'),
                array('description' => 'mr'),
                array('description' => 'chm'),
                array('description' => 'mh'),
                array('description' => 'msb'),
                array('description' => 'mi'),
                array('description' => 'pdt'),
                array('description' => 'apm'),
                array('description' => 'mic'),
                array('description' => 'nan'),
                array('description' => 'min'),
                array('description' => 'mn'),
                array('description' => 'mdf'),
                array('description' => 'new'),
                array('description' => 'bsk'),
                array('description' => 'bzd'),
                array('description' => 'moh'),
                array('description' => 'nv'),
                array('description' => 'nah'),
                array('description' => 'na'),
                array('description' => 'nap'),
                array('description' => 'nsk'),
                array('description' => 'ng'),
                array('description' => 'ne'),
                array('description' => 'nog'),
                array('description' => 'no'),
                array('description' => 'nn'),
                array('description' => 'nr'),
                array('description' => 'ii'),
                array('description' => 'prs'),
                array('description' => 'dz'),
                array('description' => 'dv'),
                array('description' => 'din'),
                array('description' => 'ota'),
                array('description' => 'ryu'),
                array('description' => 'nl'),
                array('description' => 'oc'),
                array('description' => 'osa'),
                array('description' => 'os'),
                array('description' => 'cy'),
                array('description' => 'hu'),
                array('description' => 'udm'),
                array('description' => 'uz'),
                array('description' => 'ug'),
                array('description' => 'uk'),
                array('description' => 'wo'),
                array('description' => 'ur'),
                array('description' => 'pau'),
                array('description' => 'pln'),
                array('description' => 'pi'),
                array('description' => 'pag'),
                array('description' => 'pap'),
                array('description' => 'ps'),
                array('description' => 'pms'),
                array('description' => 'fa'),
                array('description' => 'pcd'),
                array('description' => 'pl'),
                array('description' => 'pt'),
                array('description' => 'fuc'),
                array('description' => 'rap'),
                array('description' => 'rhg'),
                array('description' => 'rom'),
                array('description' => 'cgg'),
                array('description' => 'ro'),
                array('description' => 'rue'),
                array('description' => 'ru'),
                array('description' => 'sm'),
                array('description' => 'sg'),
                array('description' => 'sa'),
                array('description' => 'sc'),
                array('description' => 'ceb'),
                array('description' => 'sr'),
                array('description' => 'srr'),
                array('description' => 'trv'),
                array('description' => 'scn'),
                array('description' => 'si'),
                array('description' => 'sd'),
                array('description' => 'sco'),
                array('description' => 'mk'),
                array('description' => 'sk'),
                array('description' => 'sl'),
                array('description' => 'so'),
                array('description' => 'sn'),
                array('description' => 'st'),
                array('description' => 'sw'),
                array('description' => 'swg'),
                array('description' => 'sv'),
                array('description' => 'su'),
                array('description' => 'syl'),
                array('description' => 'ss'),
                array('description' => 'tl'),
                array('description' => 'th'),
                array('description' => 'ty'),
                array('description' => 'tzm'),
                array('description' => 'ta'),
                array('description' => 'tmh'),
                array('description' => 'tt'),
                array('description' => 'crh'),
                array('description' => 'tg'),
                array('description' => 'te'),
                array('description' => 'tet'),
                array('description' => 'ti'),
                array('description' => 'tli'),
                array('description' => 'jam'),
                array('description' => 'to'),
                array('description' => 'tpi'),
                array('description' => 'x'),
                array('description' => 'srn'),
                array('description' => 'tdn'),
                array('description' => 'tw'),
                array('description' => 'tr'),
                array('description' => 'tk'),
                array('description' => 'tus'),
                array('description' => 'tn'),
                array('description' => 'chr'),
                array('description' => 'ce'),
                array('description' => 'cs'),
                array('description' => 'ny'),
                array('description' => 'ts'),
                array('description' => 'cv'),
                array('description' => 'yua'),
                array('description' => 'fi'),
                array('description' => 'vls'),
                array('description' => 'ff'),
                array('description' => 'fur'),
                array('description' => 'haw'),
                array('description' => 'hak'),
                array('description' => 'ch'),
                array('description' => 'kca'),
                array('description' => 'ha'),
                array('description' => 'hz'),
                array('description' => 'ho'),
                array('description' => 'km'),
                array('description' => 'hmn'),
                array('description' => 'xh'),
            )
        );

        \DB::table('language_levels')->insert(
            array(
                array('description'   => 'Επιλέξτε επίπεδο'),
                array('description'   => 'Κακό'),
                array('description'   => 'Κάτω του μετρίου'),
                array('description'   => 'Μέτριο'),
                array('description'   => 'Καλό'),
                array('description'   => 'Άριστο'),
            )
        );
    }
}
