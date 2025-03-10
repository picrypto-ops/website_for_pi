#!/usr/bin/env python3
import argparse
import sys
from file_extractor import FileExtractor


def parse_arguments():
    """Parse command line arguments."""
    parser = argparse.ArgumentParser(
        description='Extract files with specific extensions from folders and export them.',
        formatter_class=argparse.ArgumentDefaultsHelpFormatter
    )
    
    parser.add_argument(
        '-e', '--extensions',
        required=True,
        nargs='+',
        help='List of file extensions to extract (without the dot)'
    )
    
    parser.add_argument(
        '-f', '--folders',
        nargs='+',
        default=[],
        help='List of folders to search in'
    )
    
    parser.add_argument(
        '-x', '--exclude',
        nargs='+',
        default=[],
        help='List of folders to exclude from the search'
    )
    
    parser.add_argument(
        '-i', '--include-files',
        nargs='+',
        default=[],
        help='List of specific files to always include in the export'
    )
    
    parser.add_argument(
        '-o', '--output',
        required=True,
        help='Folder where the exported files will be saved'
    )
    
    args = parser.parse_args()
    
    # Validate that either folders or include files are provided
    if not args.folders and not args.include_files:
        parser.error("At least one of --folders or --include-files must be provided")
    
    return args


def main():
    """Main entry point for the script."""
    try:
        args = parse_arguments()
        
        extractor = FileExtractor(
            extension_list=args.extensions,
            folder_list=args.folders,
            exclude_folder_list=args.exclude,
            saved_export_folder=args.output,
            include_file_list=args.include_files
        )
        
        extractor.run()
        return 0
    
    except KeyboardInterrupt:
        print("\nOperation cancelled by user")
        return 1
    except Exception as e:
        print(f"Error: {str(e)}")
        return 1


if __name__ == "__main__":
    sys.exit(main()) 