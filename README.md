# File Extractor

A Python utility that extracts files with specific extensions from specified folders, excluding certain folders, and exports their contents to separate files by extension.

## Features

- Search for files with specific extensions in multiple folders and their subfolders
- Exclude specified folders from the search
- Include specific files regardless of their location or extension
- Export file contents to separate files by extension
- Include file headers with file name and relative path for each exported file
- Comprehensive error handling
- Command-line interface for easy use

## Requirements

- Python 3.6 or higher

## Installation

Clone this repository or download the files:

```bash
git clone https://github.com/yourusername/file-extractor.git
cd file-extractor
```

## Usage

### Using the FileExtractor class in your code

```python
from file_extractor import FileExtractor

extractor = FileExtractor(
    extension_list=["py", "js", "css"],
    folder_list=["./src"],
    exclude_folder_list=["./src/vendor"],
    saved_export_folder="./exports",
    include_file_list=["./config.json", "./special_file.php", "./.htaccess"]
)
extractor.run()
```

### Using the command-line interface

```bash
python file_extractor_cli.py -e py js css -f ./src -x ./src/vendor -i ./config.json ./special_file.php ./.htaccess -o ./exports
```

#### Command-line arguments

- `-e, --extensions`: List of file extensions to extract (without the dot) (required)
- `-f, --folders`: List of folders to search in
- `-x, --exclude`: List of folders to exclude from the search (optional)
- `-i, --include-files`: List of specific files to always include in the export, regardless of their extension (optional)
- `-o, --output`: Folder where the exported files will be saved (required)

Note: At least one of `--folders` or `--include-files` must be provided.

## Example

```bash
# Extract all Python, JavaScript, and CSS files from ./src and ./lib directories,
# excluding ./src/vendor and ./lib/node_modules directories,
# always include specific files regardless of location or extension,
# and save the results to ./exports directory
python file_extractor_cli.py -e py js css -f ./src ./lib -x ./src/vendor ./lib/node_modules -i ./config.json ./.htaccess ./important.php -o ./exports
```

## Output

For each extension specified, a file named `export_<extension>.txt` will be created in the specified output folder. Additionally, files with extensions not specified in the extension list but included via `include_file_list` will be exported to separate files based on their extension.

Each file will contain the contents of all matching files, with headers indicating the file name and relative path (from the current working directory).

Example header:

```
================================================================================
FILE: example.py
PATH: src/utils/example.py
================================================================================

# File content follows...
```

## Error Handling

The utility includes comprehensive error handling:

- Validation of input parameters
- Handling of missing folders
- Error reporting for file read/write issues
- Graceful handling of encoding issues

All errors are logged and, where appropriate, included in the exported files. 