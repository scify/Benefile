# BeneFile landing page

The landing page of [BeneFile](https://github.com/scify/Benefile), in English (`index.html`) and Greek (`el/index.html`). GitHub Pages publishes this folder at https://scify.github.io/Benefile/, through the workflow `.github/workflows/pages.yml`.

- Plain HTML and CSS, with no build step. Before the visitor's consent, the page makes no requests to other sites.
- The colours are the colours of the application (`assets/css/style.css`).
- The screenshots in `assets/img/` (English) and `assets/img/el/` (Greek) come from a local installation with example data. The names in them are not real people.
- The animated hero is a small script built from `hero/`. `hero/README.md` tells how to change it.

To change the page, edit the files in `site/` and push to `master`. The workflow publishes the change in about a minute.

## Google Analytics and cookie consent

The page uses the cookie consent of the SciFY static sites (`_consent/`, the same files as the sites in the `html-pages` repository). Google Analytics loads **only after the visitor accepts** analytics in the cookie banner. Before that, the page sends nothing to Google and sets no analytics cookie.

No ID is set yet (`gaId: ""`), so Google Analytics does not run until you add one.

1. In Google Analytics, open *Admin › Data streams › (the web stream)* and copy the **Measurement ID** (`G-XXXXXXXXXX`).
2. Put it in `gaId` in [`_consent/config.js`](_consent/config.js), and push. Both pages load this file.

| File | What it is |
|------|------------|
| `_consent/config.js` | **Settings**: Google Analytics ID, privacy policy link, banner language. |
| `_consent/consent.js` | Banner, preferences dialog and Google Analytics loader. Do not change it here: it is the same on all SciFY static sites. |
| `_consent/consent.css` | Styles of the banner and the dialog. The colours of BeneFile are in `assets/css/style.css`. |

The "Cookie settings" button in the footer opens the preferences dialog. To test the banner again, run `SciFYConsent.reset()` in the browser console.

To check it: open the page in a private window, with the *Network* tab of the developer tools open. Before you accept, there is no request to `googletagmanager.com`. After "Accept analytics", `gtag/js?id=G-…` loads.
