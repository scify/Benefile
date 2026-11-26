# The animated hero

The hero of the landing page shows BeneFile at work: a doctor adds a medical visit to a file. The new visit appears at the top of the medical history, and the file history shows the doctor's work above the work of the psychologist and the lawyer. The scene is in English on `index.html` and in Greek on `el/index.html` (from `<html lang>`).

The scene is made with the Stageshow kit, a copy of which is in `src/kit/` (MIT licence, `src/kit/LICENSE`). The logos in `src/kit/brands/` have their own licences (`src/kit/brands/SOURCES.md`); the hero does not use them, and the build leaves them out. It is a drawing of the application, not the application: it fetches nothing and sends nothing.

## Files

| File | What it is |
|------|------------|
| `src/scene/BenefileHero.tsx` | The scene: the screen, the words in both languages, and the script of the story. |
| `src/scene/benefile.css` | The colours and the layout, in the colours of the application. |
| `src/main.tsx` | Puts the scene in `#hero-demo`, and connects the Pause button of the page. |
| `../assets/hero/` | The build: `hero.js` and `hero.css`. Do not edit them. |
| `../assets/img/hero/` | Stills of the scene, for visitors without JavaScript and while the script loads. |

## Change it

```shell
npm install
npm run build
```

The build writes `../assets/hero/`. Commit the build together with the source.

After a change to the scene, make the stills again: open each page with reduced motion (the scene then shows its last frame), and save the hero at 1280 px and at 452 px wide, with a transparent background. Save them as `poster-<en|el>.webp` and `poster-<en|el>-compact.webp` in `../assets/img/hero/`.

## Accessibility

- The scene has `role="img"` and a description in the page's language.
- With reduced motion it shows one still frame.
- Otherwise the page shows a Pause button, which stops the scene on its still frame.
