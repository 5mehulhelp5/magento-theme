# Forever_DemoInstaller

Multi-demo data import/export for the Forever Magento 2 theme (Furniture, Fashion, Clothing,
Electronics, ...). Imports CMS pages/blocks, widgets, store configuration, theme assignment,
and any custom-module table data (Banner Slider, Mega Menu, Blog, Brand, Testimonials, Team,
FAQ, Store Locator) from self-contained demo packages.

## Install

```bash
# copy this folder to app/code/Forever/DemoInstaller in your Magento root, then:
php bin/magento module:enable Forever_DemoInstaller
php bin/magento setup:upgrade
php bin/magento setup:di:compile        # production mode only
php bin/magento cache:flush
```

Run the commands as the web-server user so files written to pub/media keep correct ownership.

## CLI

```bash
php bin/magento forever:demo:list
php bin/magento forever:demo:import furniture --store=1
php bin/magento forever:demo:import fashion --store=1 --types=cms_page,cms_block,widget --no-media -f
php bin/magento forever:demo:export furniture --store=1
```

Import options: `--store/-s`, `--types/-t` (comma list), `--no-media`, `--no-overwrite`, `--force/-f`.

## Admin

Content -> Demo Importer -> "Import this demo" on a card.

## Demo package format

```
data/demo/<code>/
  manifest.json        # code, label, version, thumbnail, ordered steps
  cms_pages.xml        # <root><pages><cms_item>...   (same format as Forever_Core/etc/import)
  cms_blocks.xml       # <root><blocks><cms_item>...
  widgets.json         # widget_instance rows (theme referenced by theme_full_path)
  config.json          # [{ path, value, scope, scope_id }]
  <table>.json         # one file per custom table; column-agnostic upsert
  media/               # copied verbatim into pub/media (incl. thumbnail.jpg)
```

### Step types (manifest "steps")

| type      | keys                                   | action                                      |
|-----------|----------------------------------------|---------------------------------------------|
| media     | source                                 | copy media/* into pub/media                 |
| cms_block | source                                 | upsert CMS blocks by identifier             |
| cms_page  | source                                 | upsert CMS pages by identifier              |
| widget    | source                                 | upsert widget instances by title+type       |
| table     | source, table, unique[], truncate?     | upsert rows into any custom table           |
| config    | source                                 | write core_config_data values               |
| theme     | theme (frontend/Forever/<theme>)       | assign theme to the store                   |

All importers are idempotent (match on a natural key, update-or-create). Each `table`
step runs inside a DB transaction.

## Building the other demos

1. Configure a store the way you want it.
2. `php bin/magento forever:demo:export <code> --store=<id>` regenerates the JSON/config files
   in `data/demo/<code>/` (the manifest must already exist — the fashion/clothing/electronics
   folders ship with skeleton manifests).
3. Drop matching images into `data/demo/<code>/media/` and a `thumbnail.jpg`.

## Notes / scope

- Products & categories are intentionally out of scope — ship those via Magento sample-data
  modules or product-CSV import (`bin/magento import`). A `catalog_csv` importer can be added.
- Widget instances are stored by `theme_full_path` for portability and re-resolved on import.
- The importers OVERWRITE by design. Back up the DB before running on a populated store.
- No existing Forever module is modified; this module is fully self-contained.
