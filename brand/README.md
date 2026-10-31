# brand/ — the family's real material

`php artisan db:seed` imports everything here (once). After that, the family edits it all in `/admin`.

Current content was taken from the family's Facebook (profile 61587800821979) on 2026-10-31.

| File | What it is |
|---|---|
| `donate.txt` | GCash, banks, GoFundMe and Facebook details (`Label: value` lines; see `donate.example.txt`). |
| `gcash-qr.jpg`, `bank-qr.jpg`, `bank2-qr.jpg` | QR codes. They're checked to still scan after WebP conversion. |
| `photos/` | `hero.jpg` is the main photo; the others appear in the story. `captions.txt` holds the description (alt text) and caption for each photo. |
| `videos/` | Short reels (compressed to 540p), a same-name `.jpg` poster for each, and `captions.txt`. |
| `shop/` | Posters for "Buy from the family". Their text and prices are in `database/seeders/content/site.php`. |
| `proof/` | Medical reports **with ID numbers, birthdate, record numbers and doctors' signatures covered**. **Not committed to git**: upload these by hand when deploying. |
| `story.txt` | Optional. Used word for word only if `story_en` in `content/site.php` is set to `null`. |

Photos are resized to WebP (max 1600px, under 200 KB) and their hidden location data is removed.

Photos where David's son can be identified were left out on purpose.
