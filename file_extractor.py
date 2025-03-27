import os
import logging
from pathlib import Path
from typing import List, Dict, Set


class FileExtractor:
    """
    A class that extracts files with specific extensions from specified folders,
    excluding certain folders, and exports their contents to separate files by extension.
    """

    def __init__(
        self,
        extension_list: List[str],
        folder_list: List[str],
        exclude_folder_list: List[str],
        saved_export_folder: str,
        include_file_list: List[str] = []
    ):
        """
        Initialize the FileExtractor with the given parameters.

        Args:
            extension_list: List of file extensions to extract (without the dot)
            folder_list: List of folders to search in
            exclude_folder_list: List of folders to exclude from the search
            saved_export_folder: Folder where the exported files will be saved
            include_file_list: List of specific files to always include in the export
        """
        self.extension_list = [ext.lower().lstrip('.') for ext in extension_list]
        self.folder_list = [os.path.abspath(folder) for folder in folder_list]
        self.exclude_folder_list = [os.path.abspath(folder) for folder in exclude_folder_list]
        self.saved_export_folder = os.path.abspath(saved_export_folder)
        self.include_file_list = [os.path.abspath(file) for file in include_file_list]
        self.files_by_extension: Dict[str, List[str]] = {ext: [] for ext in self.extension_list}
        # Keep track of explicitly included files with extensions not in extension_list
        self.additional_extensions: Set[str] = set()
        
        # Store the current working directory to calculate relative paths
        self.base_path = os.getcwd()
        
        # Configure logging
        logging.basicConfig(
            level=logging.INFO,
            format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
        )
        self.logger = logging.getLogger(__name__)

    def is_excluded_path(self, path: str) -> bool:
        """
        Check if a path is in the excluded folders list.

        Args:
            path: The path to check

        Returns:
            True if the path is excluded, False otherwise
        """
        path = os.path.abspath(path)
        for excluded in self.exclude_folder_list:
            if path.startswith(excluded):
                return True
        return False

    def get_relative_path(self, path: str) -> str:
        """
        Convert an absolute path to a relative path from the base directory.

        Args:
            path: The absolute path to convert

        Returns:
            The relative path from the base directory
        """
        try:
            return os.path.relpath(path, self.base_path)
        except ValueError:
            # If paths are on different drives, return the original path
            return path

    def find_matching_files(self) -> Dict[str, List[str]]:
        """
        Find all files with the specified extensions in the specified folders,
        excluding the specified excluded folders.
        Also include any files from include_file_list regardless of their extension.

        Returns:
            Dictionary mapping extensions to lists of file paths
        """
        self.logger.info("Searching for files with extensions: %s", self.extension_list)
        
        # Start with existing extensions
        matched_files: Dict[str, List[str]] = {ext: [] for ext in self.extension_list}
        
        try:
            # Process included files first
            for file_path in self.include_file_list:
                if not os.path.exists(file_path):
                    self.logger.warning(f"Included file does not exist: {file_path}")
                    continue
                    
                if not os.path.isfile(file_path):
                    self.logger.warning(f"Included path is not a file: {file_path}")
                    continue
                    
                ext = os.path.splitext(file_path)[1].lower().lstrip('.')
                
                # If the file has no extension, use a special key
                if not ext:
                    ext = "no_extension"
                
                # Always include files from include_file_list, regardless of extension
                if ext not in matched_files:
                    # Track new extensions for explicitly included files
                    self.additional_extensions.add(ext)
                    matched_files[ext] = []
                    
                matched_files[ext].append(file_path)
                self.logger.info(f"Added included file: {file_path}")
            
            # Process folders
            for folder in self.folder_list:
                if not os.path.exists(folder):
                    self.logger.warning(f"Folder does not exist: {folder}")
                    continue
                    
                for root, dirs, files in os.walk(folder):
                    # Skip excluded directories
                    if self.is_excluded_path(root):
                        continue
                    
                    for file in files:
                        file_path = os.path.join(root, file)
                        # Skip if the file is already included from include_file_list
                        file_path_abs = os.path.abspath(file_path)
                        if file_path_abs in self.include_file_list:
                            continue
                            
                        ext = os.path.splitext(file)[1].lower().lstrip('.')
                        
                        if ext in self.extension_list:
                            matched_files[ext].append(file_path)
        except Exception as e:
            self.logger.error(f"Error during file search: {str(e)}")
            raise
            
        total_files = sum(len(files) for files in matched_files.values())
        self.logger.info("Found %s matching files", total_files)
        
        # Log additional extensions that were included
        if self.additional_extensions:
            self.logger.info(f"Including additional extensions from explicitly included files: {', '.join(self.additional_extensions)}")
            
        return matched_files

    def process_file(self, file_path: str) -> str:
        """
        Read a file and return its contents with a header containing the file name and path.

        Args:
            file_path: Path to the file to process

        Returns:
            String containing the header and the file contents
        """
        try:
            with open(file_path, 'r', encoding='utf-8', errors='replace') as f:
                content = f.read()
            
            # Get the relative path for display in the header
            relative_path = self.get_relative_path(file_path)
            
            header = f"\n{'='*80}\n"
            header += f"FILE: {os.path.basename(file_path)}\n"
            header += f"PATH: {relative_path}\n"
            header += f"{'='*80}\n\n"
            
            return header + content
        except IOError as e:
            self.logger.error(f"Error reading file {file_path}: {str(e)}")
            return f"\n{'='*80}\nERROR reading file {file_path}: {str(e)}\n{'='*80}\n"
        except Exception as e:
            self.logger.error(f"Unexpected error processing file {file_path}: {str(e)}")
            return f"\n{'='*80}\nERROR processing file {file_path}: {str(e)}\n{'='*80}\n"

    def export_files(self, files_by_extension: Dict[str, List[str]]) -> None:
        """
        Export the processed files to separate files by extension.

        Args:
            files_by_extension: Dictionary mapping extensions to lists of file paths
        """
        # Create the export folder if it doesn't exist
        os.makedirs(self.saved_export_folder, exist_ok=True)
        
        for ext, file_paths in files_by_extension.items():
            if not file_paths:
                self.logger.info(f"No files found with extension: {ext}")
                continue
                
            export_file_path = os.path.join(self.saved_export_folder, f"export_{ext}.txt")
            self.logger.info(f"Exporting {len(file_paths)} files with extension '{ext}' to {export_file_path}")
            
            try:
                with open(export_file_path, 'w', encoding='utf-8') as export_file:
                    for file_path in file_paths:
                        processed_content = self.process_file(file_path)
                        export_file.write(processed_content)
                        export_file.write("\n\n")
                        
                self.logger.info(f"Successfully exported files with extension '{ext}'")
            except IOError as e:
                self.logger.error(f"Error writing to export file {export_file_path}: {str(e)}")
            except Exception as e:
                self.logger.error(f"Unexpected error exporting files with extension '{ext}': {str(e)}")

    def run(self) -> None:
        """
        Run the entire file extraction and export process.
        """
        try:
            self.logger.info("Starting file extraction process")
            
            # Validate inputs
            if not self.extension_list and not self.include_file_list:
                raise ValueError("Both extension list and include file list cannot be empty")
            if not self.folder_list and not self.include_file_list:
                raise ValueError("Both folder list and include file list cannot be empty")
            if not self.saved_export_folder:
                raise ValueError("Export folder cannot be empty")
                
            # Find matching files
            matched_files = self.find_matching_files()
            
            # Export files
            self.export_files(matched_files)
            
            self.logger.info("File extraction process completed successfully")
        except Exception as e:
            self.logger.error(f"Error in file extraction process: {str(e)}")
            raise


if __name__ == "__main__":
    # Example usage
    extractor = FileExtractor(
        # extension_list=["php", "js", "scss"],
        extension_list=["php", "js", "scss", "css", "json"],
        folder_list=["./pi_website_take5/assets/css", "./pi_website_take5/assets/js", "./pi_website_take5/assets/scss", "./pi_website_take5/src"],
        # folder_list=["./pi_website_take5/assets", './pi_website_take5/components', './pi_website_take5/includes', './pi_website_take5/pages'],
        # exclude_folder_list=["./pi_website_take5/assets/images", "./pi_website_take5/assets/scss-bem"],
        exclude_folder_list=["./pi_website_take5/src/utility", "./pi_website_take5/src/src/scss"],
        saved_export_folder="./export_scss",
        include_file_list=["./pi_website_take5/.htaccess", "./pi_website_take5/index.php"]
    )
    extractor.run()