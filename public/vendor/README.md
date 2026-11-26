# Front-end libraries

The pages load these files from this folder, never from another site.
Each file is a copy of the file in the npm package, with no change.
The versions are in `package.json` (`devDependencies`), with exact numbers.

| Folder | Package | Version | Licence | Use |
|---|---|---|---|---|
| `simple-datatables/` | [simple-datatables](https://github.com/fiduswriter/simple-datatables) | 10.3.0 | LGPL-3.0 | Tables with sorting, search and paging |
| `tom-select/` | [tom-select](https://tom-select.js.org) | 2.7.0 | Apache-2.0 | Lists with search: medicines, ICD-10 conditions |
| `vanillajs-datepicker/` | [vanillajs-datepicker](https://mymth.github.io/vanillajs-datepicker/) | 1.3.4 | MIT | Calendars of the date fields (dd-mm-yyyy) |
| `dropzone/` | [dropzone](https://www.dropzone.dev) | 4.2.0 | MIT | Upload of the CSV import |

None of them needs jQuery. The charts (`public/js/amcharts`) are amCharts 3, which also works without jQuery.

## Update a library

1. Choose a version that was published at least two weeks ago.
2. Install it with its exact number, for example `npm install --save-dev --save-exact tom-select@2.7.0`.
3. Copy the files again:

   ```shell
   cp node_modules/simple-datatables/dist/umd/simple-datatables.js node_modules/simple-datatables/dist/style.css node_modules/simple-datatables/LICENSE public/vendor/simple-datatables/
   cp node_modules/tom-select/dist/js/tom-select.complete.min.js node_modules/tom-select/dist/css/tom-select.default.min.css node_modules/tom-select/LICENSE public/vendor/tom-select/
   cp node_modules/vanillajs-datepicker/dist/js/datepicker.min.js node_modules/vanillajs-datepicker/dist/css/datepicker.min.css node_modules/vanillajs-datepicker/LICENSE public/vendor/vanillajs-datepicker/
   cp node_modules/vanillajs-datepicker/dist/js/locales/el.js public/vendor/vanillajs-datepicker/locales/
   cp node_modules/dropzone/dist/min/dropzone.min.js node_modules/dropzone/LICENSE public/vendor/dropzone/
   ```

4. Change the version in the table above.
5. Run the browser tests (`npx playwright test`).
