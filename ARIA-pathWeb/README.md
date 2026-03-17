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



