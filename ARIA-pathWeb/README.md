# ARIA-pathWeb

Laravel frontend for ARIA-path.

## Prerequisites

- PHP and Composer
- MySQL (or compatible database)
- Web server (Apache or Nginx)

## Installation

1. Install dependencies.

```bash
composer install
```

2. Create environment file.

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure database in `.env`.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aria_path
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

4. Run migrations and seeders.

```bash
php artisan migrate:refresh --seed
```

## Configuration Notes

### API endpoint setup for ModelSeeder

`ModelSeeder` reads API endpoint values from `.env`.

Add or update these variables:

```dotenv
API_IP=api.example.org/aria-pathapi
API_SCHEME=https
API_WS_SCHEME=wss
```

Meaning of each variable:

- `API_IP`: single base host for all seeded endpoints.
- `API_IP` is used for all endpoint families: `/deepzoom`, `/annotation`, `/copilot`, `/segment`, and websocket `/stream`.
- `API_IP` can be `host[:port]`, `host/prefix-path`, or full URL.
- `API_SCHEME`: `http` or `https` for normal API calls.
- `API_WS_SCHEME`: `ws` or `wss` for websocket endpoints (optional, can be left empty).
- `API_USE_SERVICE_PORTS`: set to `true` to build endpoints with separate ports per service.
- `API_PORT_DEEPZOOM`, `API_PORT_ANNOTATION`, `API_PORT_NUCLEI`, `API_PORT_COPILOT`, `API_PORT_SEGMENT`: service ports when `API_USE_SERVICE_PORTS=true`.
- `API_PORT_STREAM`: websocket stream port (optional, defaults to `API_PORT_ANNOTATION`).

How URL assembly works in seeded API records:

- HTTP endpoints use: `{API_SCHEME}://{API_IP}/deepzoom/...`, `/annotation/...`, `/copilot...`, `/segment...`
- Websocket endpoint uses: `{API_WS_SCHEME}://{API_IP}/stream?...`

About postfix paths like `/deepzoom`, `/annotation`, `/copilot`, `/segment`:

- These postfix names can vary depending on your reverse proxy routing.
- If your backend services run on different ports, use a reverse proxy/API gateway as the single `API_IP` entry point.
- A direct `IP + port` value works only when that single exposed port already routes all required paths.
- Keep proxy target ports consistent with `docker-compose.yml` in `ARIA-pathAPI`.

Examples:

```dotenv
# Reverse proxy example
API_IP=api.example.org/aria-pathapi
API_SCHEME=https
API_WS_SCHEME=wss

# Direct service port example
# (single exposed port that routes all required paths)
API_IP=127.0.0.1:8001
API_SCHEME=http
API_WS_SCHEME=ws

# Multi-port backend example (recommended via reverse proxy)
# internal services can be 8001/8002/8003..., but expose one external base URL:
API_IP=192.168.1.20/aria-pathapi
API_SCHEME=http
API_WS_SCHEME=ws

# Direct IP + per-service ports example
API_IP=192.168.1.20
API_SCHEME=http
API_WS_SCHEME=ws
API_USE_SERVICE_PORTS=true
API_PORT_DEEPZOOM=8001
API_PORT_ANNOTATION=8002
API_PORT_NUCLEI=8003
API_PORT_COPILOT=8004
API_PORT_SEGMENT=8005
# Optional, defaults to API_PORT_ANNOTATION when empty
API_PORT_STREAM=8002
```

When `API_USE_SERVICE_PORTS=true`, ModelSeeder generates URLs like:

- slide/scale: `{API_SCHEME}://{API_IP}:{API_PORT_DEEPZOOM}/deepzoom/...`
- annotation CRUD/search/count: `{API_SCHEME}://{API_IP}:{API_PORT_ANNOTATION}/annotation/...`
- nuclei: `{API_SCHEME}://{API_IP}:{API_PORT_NUCLEI}/nuclei/...`
- copilot: `{API_SCHEME}://{API_IP}:{API_PORT_COPILOT}/copilot...`
- segment: `{API_SCHEME}://{API_IP}:{API_PORT_SEGMENT}/segment...`
- stream: `{API_WS_SCHEME}://{API_IP}:{API_PORT_STREAM}/stream...`

### Image storage symlink

Create the public symlink so Laravel can access slide/image storage.

```bash
cd ARIA-pathWeb/public
ln -s /data/web/ARIA-path images
```

### File permissions

```bash
cd /data/web/ARIA-path
find ./ -type d -print0 | xargs -0 chmod 0755
find ./ -type f -print0 | xargs -0 chmod 0644
```

### Optional: libvips for generating thumbnail images


```bash
conda install conda-forge::libvips
```

## Upload Images

### Directory layout

Upload original images to `/data/web/ARIA-path/original/` using the following folder hierarchy:

```text
/data/web/ARIA-path/original/
├── project_codename/
│   ├── dataset_codename/
│   │   ├── sub_dataset_codename/
│   │   │   ├── [Image files]
│   │   │   └── ...
│   │   └── ...
│   └── ...
└── ...
```

If the original images (`svs`) have HD result images (`tiff` or `dzi`), upload those result images to `/data/web/ARIA-path/mask/` using the same directory structure as the original folder. The HD result images must have the same filename as the corresponding original image, but may use a different suffix.

### File permissions

```bash
cd /data/web/ARIA-path
find ./ -type d -print0 | xargs -0 chmod 0755
find ./ -type f -print0 | xargs -0 chmod 0644
```

### Prepare CSV file

Create a CSV file to store image metadata for loading into the database.

- A template CSV file is available at `public/input_template.csv`.
- Make a copy of the template and fill in the image information.
- Do not change the column structure, otherwise loading can fail.
- The first four columns are required.
- The values in the first four columns must exactly match the image and folder names in the directory structure.
- Use the format `<projectname>-uuid.csv` to name the file, for example `default-uuid.csv`.
- Upload the completed CSV file to `/data/web/ARIA-path/csv/`.
- The web page project menu can use this CSV naming pattern so users can download the CSV file for each project.

In the CSV file, only record the original images. If matching HD result images exist in the mask folder, ARIA-path will automatically load them in dual-view mode. If there is no HD result image, the dual-view mode will show the original image in both panes.

The loading procedure automatically detects supported image types: `svs`, `tiff`, and `dzi`.

### Special case: nested sub-dataset folders

If there are nested folders inside a sub-dataset, record the full relative nested path in the `sub_dataset` column.

Note: on the web page, users only see the project and dataset levels in the main menu. Nested `sub_dataset` paths are used for backend organization and image loading, but are not shown as separate navigation levels in the UI.

Examples:

- If the sub-dataset is `XYZ`, set `sub_dataset` to `XYZ`.
- If there is a folder `ABC` under `XYZ`, set `sub_dataset` to `XYZ/ABC`.
- If there is a folder `RST` under `ABC`, set `sub_dataset` to `XYZ/ABC/RST`.

This allows the loader to support an unlimited number of nested subdirectories.
![Loading Image](public/img/iviewer-loading.png "Image Loading")    

## Load Image Information To Database

After both the images and the CSV file are uploaded, load the image metadata into the database.

CAUTION: upload the images first, then run the seeder.

```bash
cd /var/www/laravel/ARIA-pathWeb
TEAM_ID=XXX CSV_FILE=YYY php artisan db:seed --class=ImageSeeder

# example
TEAM_ID=1 CSV_FILE=test_78 php artisan db:seed --class=ImageSeeder
```

Notes:

- `CSV_FILE` should be the filename without the `.csv` extension.
- To find the provider team ID, log in as an Admin and check the team list.
- If the provider team does not exist, create it before loading images.
- If needed, create accounts for new users and assign them to the correct team.
- This import flow is semi-automatic: images in one import should belong to the same provider team.
- If images from different teams are being imported, repeat the procedure separately for each team.
- If the batch name already exists, new images will be added to that existing batch.
- If you want a separate batch, rename the batch in the CSV to a unique value before loading.

When the command completes successfully:

- The CSV data is parsed and inserted into the `batch` and `image` tables.



