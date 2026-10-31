<?php

/*
|--------------------------------------------------------------------------
| Website copy
|--------------------------------------------------------------------------
| The words the site starts with. Everything here can be changed later in
| /admin. Facts come only from the family's own Facebook posts, reels,
| posters and medical reports (see TO-CONFIRM.md for the sources). Anything
| the family still has to confirm is listed in TO-CONFIRM.md.
|
| story_en: when null, the seeder uses brand/story.txt word for word if it
| exists, otherwise story_en_placeholder.
*/

return [

    'settings' => [
        'hero_headline' => 'Help David get the tests he needs to start treatment',
        'hero_subline' => 'David, 37, has a rare autoimmune skin disease caused by a tumor his doctors still cannot identify. His wife cares for him every day. The next tests will help find the right treatment.',
        'hero_headline_fil' => 'Tulungan si David na matapos ang mga test para masimulan ang kanyang gamutan',
        'hero_subline_fil' => 'Si David, 37, ay may bihirang autoimmune na sakit sa balat na dulot ng isang tumor na hindi pa matukoy ng mga doktor. Araw-araw siyang inaalagaan ng kanyang asawa. Ang mga susunod na test ang tutulong para mahanap ang tamang gamutan.',
        'hero_alt' => 'David smiling at home, with the marks of his skin disease on his face and arms',
        'meta_description' => 'David, 37, is a Filipino husband and father with paraneoplastic pemphigus, a rare autoimmune disease caused by a tumor doctors still cannot identify. Read his story and see how you can help.',
        'meta_description_fil' => 'Si David, 37, ay isang Pilipinong asawa at ama na may paraneoplastic pemphigus, isang bihirang autoimmune na sakit na dulot ng tumor na hindi pa matukoy. Basahin ang kanyang kuwento at alamin kung paano tumulong.',
        'share_message' => "Please meet David. He is 37, a husband and a father from Caloocan, Philippines, and he has paraneoplastic pemphigus, a rare autoimmune disease caused by a tumor his doctors still cannot identify. He needs ₱52,500 for the ALK and ROS-1 FISH tests that will guide his treatment. Every share helps. 🙏",
        'share_message_fil' => "Kilalanin po si David. 37 taong gulang, asawa at ama mula sa Caloocan, at may paraneoplastic pemphigus, isang bihirang autoimmune na sakit na dulot ng tumor na hindi pa matukoy ng mga doktor. Kailangan niya ng ₱52,500 para sa ALK at ROS-1 FISH test na gagabay sa kanyang gamutan. Malaking tulong ang bawat share. 🙏",
        'currency_symbol' => '₱',
    ],

    'story_en' => <<<'TXT'
        David Malmis is 37 years old. He is a husband and a father from Caloocan, in Metro Manila. Not long ago he was healthy and full of life. His wife still keeps "the last video before you experienced a simple mouth sore."

        That sore was the beginning of paraneoplastic pemphigus, a rare autoimmune disease. A tumor in his body makes his immune system attack his own skin. It leaves painful wounds on his skin, in his mouth and on his hands, so even using his hands is hard. Most people who get this disease are around 50. David was diagnosed in his thirties.

        The tumor is a mass of about 6 centimeters near his stomach. Since March 2025 he has had biopsies and scans at the Philippine General Hospital, St. Luke's Medical Center and Dr. Jose N. Rodriguez Memorial Hospital. Still, the doctors cannot say exactly what kind of tumor it is. Until they know, they cannot choose the right treatment.

        In September 2026, specialists at St. Luke's said it may be a rare inflammatory myofibroblastic tumor (IMT). To confirm it, they asked for two more tests: a repeat ALK test and a ROS-1 FISH test. Together, these tests cost ₱52,500.

        His wife cares for him every day. She looks after his wounds, takes him to his check-ups and stays by his side on the hardest days. To pay for his medicine and tests, she bakes and sells sweets, sells dried mangoes, and even buys broken gadgets to earn a little more. In her words: "Every order helps support my husband's daily needs and medical expenses."

        They are not giving up. The right diagnosis brings them closer to the right treatment, and to the day David can hold his son again without pain.

        Thank you for reading his story. Every gift, every share and every prayer helps them keep going.
        TXT,

    'story_en_placeholder' => '',

    'story_fil' => <<<'TXT'
        Si David Malmis ay 37 taong gulang, isang asawa at ama mula sa Caloocan. Mayroon siyang paraneoplastic pemphigus, isang bihirang autoimmune na sakit. Dahil sa isang tumor sa kanyang katawan, inaatake ng sarili niyang immune system ang kanyang balat. Nagkakaroon siya ng masasakit na sugat sa balat, sa bibig at sa mga kamay.

        Mula Marso 2025, ilang biopsy at scan na ang ginawa sa kanya sa PGH, St. Luke's at Dr. Jose N. Rodriguez Memorial Hospital. Pero hindi pa rin matukoy ng mga doktor kung anong klaseng tumor ito, kaya hindi pa masimulan ang tamang gamutan.

        Ayon sa St. Luke's, kailangan pa niya ng ALK test at ROS-1 FISH test para makumpirma ang diagnosis. Aabot ito sa ₱52,500.

        Araw-araw siyang inaalagaan ng kanyang asawa. Para matustusan ang gamot at mga test, nagbe-bake at nagbebenta siya ng matatamis, nagbebenta ng dried mangoes, at bumibili pa ng mga sirang gadget.

        Malaking tulong ang bawat donasyon, bawat share at bawat panalangin. Maraming salamat po.
        TXT,

    // Dates and findings from the medical reports the family posted.
    'timeline' => [
        ['when_label' => 'Before', 'title' => 'A happy, healthy husband', 'body' => 'Then came what looked like a simple mouth sore.'],
        ['when_label' => 'March 2025', 'title' => 'First biopsy of a mass near his stomach', 'body' => 'The result was not clear: it showed only chronic inflammation.'],
        ['when_label' => 'April – May 2025', 'title' => 'More tests at Philippine General Hospital', 'body' => 'Doctors found spindle cell growth, but could not tell what kind of tumor it is. They asked for more tests.'],
        ['when_label' => 'September 2026', 'title' => 'Second opinion at St. Luke\'s Medical Center', 'body' => 'Specialists said it may be a rare inflammatory myofibroblastic tumor (IMT), and asked for a repeat ALK test and a ROS-1 FISH test to confirm it.'],
        ['when_label' => 'October 2026', 'title' => 'New CT scan', 'body' => 'The mass, about 6 centimeters across, is still there.'],
        ['when_label' => 'Today', 'title' => 'Waiting for the tests that will guide his treatment', 'body' => 'The ALK and ROS-1 FISH tests cost ₱52,500. With your help, he can take them and start the right treatment.'],
    ],

    // "What David needs right now".
    'needs' => [
        ['title' => 'ALK and ROS-1 FISH tests', 'description' => 'Asked for by St. Luke\'s Medical Center to confirm the type of tumor. This is the most urgent need.', 'amount' => 52500],
        ['title' => 'More lab tests and a repeat biopsy', 'description' => 'His doctors advised another biopsy because the earlier samples were not enough to identify the tumor.', 'amount' => null],
        ['title' => 'Medicine and daily care', 'description' => 'Daily medicine, care for his wounds, and the trips to his check-ups.', 'amount' => null],
    ],

    // "Buy from the family" (photos in brand/shop/). Prices from the family's own posters.
    'shop' => [
        [
            'title' => 'Sweet Treats for a Cause',
            'photo' => '01-sweet-treats.jpg',
            'description' => "Homemade by David's wife. Cheesecake brownies (9 inch) ₱535 · Custard cheesecake (6 inch) ₱399 · Leche flan cream cheese ₱185 · Almond brownies (9 inch) ₱435. North Caloocan. Message the family to order.",
        ],
        [
            'title' => 'Dried mangoes from Cebu',
            'photo' => '02-dried-mango.jpg',
            'description' => '7D Dried Mangoes 100 g ₱150 · 200 g ₱250 · 7D Dried Mangorind 100 g ₱120 · 7D Mango Bites with white or dark chocolate (80 g) ₱110. Every purchase helps support the family.',
        ],
        [
            'title' => 'Sell her your broken gadgets',
            'photo' => '03-broken-gadgets.jpg',
            'description' => "Have an old phone, tablet, laptop or digital camera that is cracked, broken or won't turn on? David's wife buys them to help pay for his treatment. Message the family so she can check the unit.",
        ],
    ],

];
