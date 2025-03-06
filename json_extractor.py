import json
import os
import argparse
from collections import defaultdict

# ===== Utility Functions =====

def read_json_file(file_path):
    """Read and parse a JSON file."""
    with open(file_path, 'r', encoding='utf-8') as file:
        return json.load(file)

def write_json_file(file_path, data):
    """Write data to a JSON file with proper formatting."""
    with open(file_path, 'w', encoding='utf-8') as file:
        json.dump(data, file, indent=2, ensure_ascii=False)

# ===== Pages Handler =====

class PagesHandler:
    @staticmethod
    def extract(data, language):
        """Extract content from pages.json for the specified language."""
        result = []
        
        for page_key, page_data in data.items():
            lang_data = page_data.get('language_slug', {}).get(language, {})
            if not lang_data:
                continue
                
            # Format: pages | page_slug | language
            header = f"pages | {page_data.get('page_slug', page_key)} | {language}"
            result.append(header)
            
            # Add each field with its value
            for field, value in lang_data.items():
                result.append(f"{field}: ")
                if isinstance(value, str):
                    result.append(f"{value}")
                elif isinstance(value, list):
                    # Handle arrays
                    for item in value:
                        if isinstance(item, dict):
                            for k, v in item.items():
                                result.append(f"  {k}: {v}")
                        else:
                            result.append(f"  {item}")
                result.append("")  # Empty line for readability
                
            result.append("\n" + "="*50 + "\n")  # Separator between pages
            
        return "\n".join(result)
    
    @staticmethod
    def update(original_data, update_text, language):
        """Update pages.json with data from update file."""
        updated_data = original_data.copy()
        
        # Parse the update text
        current_page = None
        current_field = None
        field_content = []
        
        lines = update_text.strip().split('\n')
        i = 0
        
        while i < len(lines):
            line = lines[i].strip()
            
            # Check for page header (pages | page_slug | language)
            if line.startswith('pages | '):
                parts = line.split(' | ')
                if len(parts) >= 3 and parts[2] == language:
                    current_page = parts[1]
                    current_field = None
                    field_content = []
            
            # Check for field name
            elif current_page and ': ' in line and not line.startswith(' '):
                # Save previous field if exists
                if current_field and field_content:
                    if current_page in updated_data:
                        # Simple field with a string value
                        updated_data[current_page]['language_slug'][language][current_field] = '\n'.join(field_content).strip()
                
                # Start new field
                current_field = line.split(': ')[0]
                field_content = []
                if len(line.split(': ')) > 1:
                    content = line.split(': ', 1)[1]
                    if content:
                        field_content.append(content)
            
            # Content line for current field
            elif current_page and current_field and line != '':
                field_content.append(line)
            
            # Check for separator (======)
            elif line.startswith('='):
                # Save the last field before moving to a new page
                updated_data[current_page]['language_slug'][language][current_field] = '\n'.join(field_content).strip()
                current_page = None
                current_field = None
                field_content = []
            
            i += 1
        
        # Handle the last field if we reached the end
        if current_page and current_field and field_content:
            updated_data[current_page]['language_slug'][language][current_field] = '\n'.join(field_content).strip()
        
        return updated_data

# ===== Products Handler =====

class ProductsHandler:
    @staticmethod
    def extract(data, language):
        """Extract content from products.json for the specified language."""
        result = []
        
        for segment_key, segment_data in data.items():
            for product_key, product_data in segment_data.items():
                lang_data = product_data.get('language_slug', {}).get(language, {})
                if not lang_data:
                    continue
                    
                # Format: products | segment | product | language
                header = f"products | {segment_key} | {product_key} | {language}"
                result.append(header)
                
                # Add each field with its value
                for field, value in lang_data.items():
                    result.append(f"{field}: ")
                    result.append(f"{value}")
                    result.append("")  # Empty line for readability
                    
                result.append("\n" + "="*50 + "\n")  # Separator between products
                
        return "\n".join(result)
    
    @staticmethod
    def update(original_data, update_text, language):
        """Update products.json with data from update file."""
        updated_data = original_data.copy()
        
        # Parse the update text
        current_segment = None
        current_product = None
        current_field = None
        field_content = []
        
        lines = update_text.strip().split('\n')
        i = 0
        
        while i < len(lines):
            line = lines[i].strip()
            
            # Check for product header (products | segment | product | language)
            if line.startswith('products | '):
                parts = line.split(' | ')
                if len(parts) >= 4 and parts[3] == language:
                    # Save previous field if exists
                    if current_segment and current_product and current_field and field_content:
                        updated_data[current_segment][current_product]['language_slug'][language][current_field] = '\n'.join(field_content).strip()
                    
                    current_segment = parts[1]
                    current_product = parts[2]
                    current_field = None
                    field_content = []
            
            # Check for field name
            elif current_segment and current_product and ': ' in line and not line.startswith(' '):
                # Save previous field if exists
                if current_field and field_content:
                    updated_data[current_segment][current_product]['language_slug'][language][current_field] = '\n'.join(field_content).strip()
                
                # Start new field
                current_field = line.split(': ')[0]
                field_content = []
                if len(line.split(': ')) > 1:
                    content = line.split(': ', 1)[1]
                    if content:
                        field_content.append(content)
            
            # Content line for current field
            elif current_segment and current_product and current_field and line != '':
                field_content.append(line)
            
            # Check for separator (======)
            elif line.startswith('='):
                # Save the last field before moving to a new product
                if current_segment and current_product and current_field and field_content:
                    updated_data[current_segment][current_product]['language_slug'][language][current_field] = '\n'.join(field_content).strip()
                current_segment = None
                current_product = None
                current_field = None
                field_content = []
            
            i += 1
        
        # Handle the last field if we reached the end
        if current_segment and current_product and current_field and field_content:
            updated_data[current_segment][current_product]['language_slug'][language][current_field] = '\n'.join(field_content).strip()
        
        return updated_data

# ===== Segments Handler =====

class SegmentsHandler:
    @staticmethod
    def extract(data, language):
        """Extract content from segments.json for the specified language."""
        result = []
        
        for segment_key, segment_data in data.items():
            lang_data = segment_data.get('language_slug', {}).get(language, {})
            if not lang_data:
                continue
                
            # Format: segments | segment_slug | language
            header = f"segments | {segment_data.get('segment_slug', segment_key)} | {language}"
            result.append(header)
            
            # Add each field with its value
            for field, value in lang_data.items():
                result.append(f"{field}: {value}")
                result.append("")  # Empty line for readability
                
            result.append("\n" + "="*50 + "\n")  # Separator between segments
            
        return "\n".join(result)
    
    @staticmethod
    def update(original_data, update_text, language):
        """Update segments.json with data from update file."""
        updated_data = original_data.copy()
        
        # Parse the update text
        current_segment = None
        current_field = None
        field_value = ""
        
        lines = update_text.strip().split('\n')
        i = 0
        
        while i < len(lines):
            line = lines[i].strip()
            
            # Check for segment header (segments | segment_slug | language)
            if line.startswith('segments | '):
                parts = line.split(' | ')
                if len(parts) >= 3 and parts[2] == language:
                    # Save previous field if exists
                    if current_segment and current_field and field_value:
                        updated_data[current_segment]['language_slug'][language][current_field] = field_value
                    
                    current_segment = parts[1]
                    current_field = None
                    field_value = ""
            
            # Check for field: value line
            elif current_segment and ': ' in line:
                # Save previous field if exists
                if current_field and field_value:
                    updated_data[current_segment]['language_slug'][language][current_field] = field_value
                
                # Start new field
                parts = line.split(': ', 1)
                current_field = parts[0]
                field_value = parts[1] if len(parts) > 1 else ""
            
            # Check for separator (======)
            elif line.startswith('='):
                # Save the last field before moving to a new segment
                if current_segment and current_field and field_value:
                    updated_data[current_segment]['language_slug'][language][current_field] = field_value
                current_segment = None
                current_field = None
                field_value = ""
            
            i += 1
        
        # Handle the last field if we reached the end
        if current_segment and current_field and field_value:
            updated_data[current_segment]['language_slug'][language][current_field] = field_value
        
        return updated_data

# ===== Team Handler =====

class TeamHandler:
    @staticmethod
    def extract(data, language):
        """Extract content from team.json for the specified language."""
        result = []
        
        for category in data:
            for member_data in data[category]:
                lang_data = member_data.get('language_slug', {}).get(language, {})
                if not lang_data:
                    continue
                    
                # Format: team | category | language
                header = f"team | {category} | {language}"
                result.append(header)
                
                # Add name and position first
                result.append(f"name: {lang_data.get('name', '')}")
                result.append(f"position: {lang_data.get('position', '')}")
                
                # Add other fields
                for field, value in lang_data.items():
                    if field not in ['name', 'position']:
                        result.append(f"{field}: {value}")
                
                result.append("")  # Empty line for readability
                result.append("\n" + "="*50 + "\n")  # Separator between team members
                
        return "\n".join(result)
    
    @staticmethod
    def update(original_data, update_text, language):
        """Update team.json with data from update file."""
        updated_data = original_data.copy()
        
        # Parse the update text
        current_category = None
        current_member_idx = None
        field_updates = {}
        
        lines = update_text.strip().split('\n')
        i = 0
        
        while i < len(lines):
            line = lines[i].strip()
            
            # Check for team header (team | category | language)
            if line.startswith('team | '):
                parts = line.split(' | ')
                if len(parts) >= 3 and parts[2] == language:
                    # Save previous updates if any
                    if current_category is not None and current_member_idx is not None and field_updates:
                        for field, value in field_updates.items():
                            updated_data[current_category][current_member_idx]['language_slug'][language][field] = value
                    
                    current_category = parts[1]
                    current_member_idx = None
                    field_updates = {}
            
            # Check for field: value line
            elif current_category is not None and ': ' in line:
                parts = line.split(': ', 1)
                field = parts[0]
                value = parts[1] if len(parts) > 1 else ""
                
                # If this is the name field, use it to identify the member
                if field == 'name' and value:
                    found = False
                    for idx, member in enumerate(updated_data[current_category]):
                        if member.get('language_slug', {}).get(language, {}).get('name') == value:
                            current_member_idx = idx
                            found = True
                            break
                    
                    if not found:
                        # Could handle new member creation here if needed
                        pass
                elif current_member_idx is not None:
                    field_updates[field] = value
            
            # Check for separator (======)
            elif line.startswith('='):
                # Save any pending updates
                if current_category is not None and current_member_idx is not None and field_updates:
                    for field, value in field_updates.items():
                        updated_data[current_category][current_member_idx]['language_slug'][language][field] = value
                
                current_category = None
                current_member_idx = None
                field_updates = {}
            
            i += 1
        
        # Handle any final updates
        if current_category is not None and current_member_idx is not None and field_updates:
            for field, value in field_updates.items():
                updated_data[current_category][current_member_idx]['language_slug'][language][field] = value
        
        return updated_data

# ===== Team Products Handler =====

class TeamProductsHandler:
    @staticmethod
    def extract(data, language):
        """Extract content from teams_products.json for the specified language."""
        result = []
        
        # Group by name_slug
        grouped_data = defaultdict(list)
        for entry in data.get('team_products', []):
            name_slug = entry.get('name_slug')
            if not name_slug:
                continue
                
            segment_slug = entry.get('segment_slug', '')
            product_slug = entry.get('product_slug', '')
            title = entry.get('language_slug', {}).get(language, {}).get('title', '')
            
            if segment_slug and (product_slug is not None or title):
                # Format: segment | product - title
                role = f"{segment_slug} | {product_slug or 'N/A'} - {title or 'N/A'}"
                grouped_data[name_slug].append(role)
        
        # Format the output
        for name_slug, roles in grouped_data.items():
            header = f"{name_slug} | {language}"
            result.append(header)
            
            for role in roles:
                result.append(role)
                
            result.append("")  # Empty line for readability
            result.append("\n" + "="*50 + "\n")  # Separator between team members
            
        return "\n".join(result)
    
    @staticmethod
    def update(original_data, update_text, language):
        """Update teams_products.json with data from update file."""
        updated_data = original_data.copy()
        teams_products = updated_data.get('teams_products', [])
        
        # Parse the update text
        current_name_slug = None
        role_updates = []
        
        lines = update_text.strip().split('\n')
        i = 0
        
        while i < len(lines):
            line = lines[i].strip()
            
            # Check for name_slug header (name_slug | language)
            if ' | ' in line and not line.startswith(' '):
                parts = line.split(' | ')
                if len(parts) == 2 and parts[1] == language:
                    # Process previous updates if any
                    if current_name_slug and role_updates:
                        # Remove existing entries for this name_slug
                        teams_products = [tp for tp in teams_products 
                                         if tp.get('name_slug') != current_name_slug]
                        
                        # Add updated entries
                        for role in role_updates:
                            role_parts = role.split(' | ')
                            if len(role_parts) >= 2:
                                segment_slug = role_parts[0]
                                product_title = role_parts[1] if len(role_parts) > 1 else ''
                                
                                if ' - ' in product_title:
                                    product_slug, title = product_title.split(' - ', 1)
                                else:
                                    product_slug, title = product_title, ''
                                
                                # Create new team product entry
                                new_entry = {
                                    'segment_slug': segment_slug,
                                    'product_slug': product_slug if product_slug != 'N/A' else None,
                                    'name_slug': current_name_slug,
                                    'language_slug': {
                                        language: {
                                            'title': title if title != 'N/A' else None
                                        }
                                    }
                                }
                                teams_products.append(new_entry)
                    
                    current_name_slug = parts[0]
                    role_updates = []
            
            # Role lines (segment | product - title)
            elif current_name_slug and ' | ' in line:
                role_updates.append(line)
            
            # Check for separator (======)
            elif line.startswith('='):
                # Process any pending updates
                if current_name_slug and role_updates:
                    # Remove existing entries for this name_slug
                    teams_products = [tp for tp in teams_products 
                                     if tp.get('name_slug') != current_name_slug]
                    
                    # Add updated entries
                    for role in role_updates:
                        role_parts = role.split(' | ')
                        if len(role_parts) >= 2:
                            segment_slug = role_parts[0]
                            product_title = role_parts[1] if len(role_parts) > 1 else ''
                            
                            if ' - ' in product_title:
                                product_slug, title = product_title.split(' - ', 1)
                            else:
                                product_slug, title = product_title, ''
                            
                            # Create new team product entry
                            new_entry = {
                                'segment_slug': segment_slug,
                                'product_slug': product_slug if product_slug != 'N/A' else None,
                                'name_slug': current_name_slug,
                                'language_slug': {
                                    language: {
                                        'title': title if title != 'N/A' else None
                                    }
                                }
                            }
                            teams_products.append(new_entry)
                
                current_name_slug = None
                role_updates = []
            
            i += 1
        
        # Process any final updates
        if current_name_slug and role_updates:
            # Same logic as above
            teams_products = [tp for tp in teams_products 
                            if tp.get('name_slug') != current_name_slug]
            
            for role in role_updates:
                role_parts = role.split(' | ')
                if len(role_parts) >= 2:
                    segment_slug = role_parts[0]
                    product_title = role_parts[1] if len(role_parts) > 1 else ''
                    
                    if ' - ' in product_title:
                        product_slug, title = product_title.split(' - ', 1)
                    else:
                        product_slug, title = product_title, ''
                    
                    new_entry = {
                        'segment_slug': segment_slug,
                        'product_slug': product_slug if product_slug != 'N/A' else None,
                        'name_slug': current_name_slug,
                        'language_slug': {
                            language: {
                                'title': title if title != 'N/A' else None
                            }
                        }
                    }
                    teams_products.append(new_entry)
        
        updated_data['teams_products'] = teams_products
        return updated_data

# ===== Main Functions =====

def extract_to_file(data_dir, output_dir, file_type, language):
    """Extract a specific type of data to a file."""
    handlers = {
        'pages': PagesHandler,
        'products': ProductsHandler,
        'segments': SegmentsHandler,
        'team': TeamHandler,
        'team_products': TeamProductsHandler
    }
    
    # Map file_type to actual filename (for special cases)
    filename_map = {
        'team_products': 'teams_products.json'
    }
    
    if file_type not in handlers:
        print(f"Unknown file type: {file_type}")
        return
    
    # Use the filename mapping if available, otherwise use the default naming pattern
    json_filename = filename_map.get(file_type, f"{file_type}.json")
    file_path = os.path.join(data_dir, json_filename)
    
    if not os.path.exists(file_path):
        print(f"File not found: {file_path}")
        return
    
    data = read_json_file(file_path)
    output = handlers[file_type].extract(data, language)
    
    output_path = os.path.join(output_dir, f"{file_type}_{language}.txt")
    os.makedirs(os.path.dirname(output_path), exist_ok=True)
    
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write(output)
    
    print(f"Extracted {file_type} data to {output_path}")

def update_from_file(data_dir, input_file, file_type, language):
    """Update JSON file from a text file."""
    handlers = {
        'pages': PagesHandler,
        'products': ProductsHandler,
        'segments': SegmentsHandler,
        'team': TeamHandler,
        'team_products': TeamProductsHandler
    }
    
    # Map file_type to actual filename (for special cases)
    filename_map = {
        'team_products': 'teams_products.json'
    }
    
    if file_type not in handlers:
        print(f"Unknown file type: {file_type}")
        return
    
    # Use the filename mapping if available, otherwise use the default naming pattern
    json_filename = filename_map.get(file_type, f"{file_type}.json")
    json_file = os.path.join(data_dir, json_filename)
    
    if not os.path.exists(json_file):
        print(f"JSON file not found: {json_file}")
        return
    
    if not os.path.exists(input_file):
        print(f"Input file not found: {input_file}")
        return
    
    # Read original JSON and update file
    original_data = read_json_file(json_file)
    
    with open(input_file, 'r', encoding='utf-8') as f:
        update_text = f.read()
    
    # Apply updates
    updated_data = handlers[file_type].update(original_data, update_text, language)
    
    # Create backup of original file
    backup_file = f"{json_file}.bak"
    write_json_file(backup_file, original_data)
    print(f"Created backup of original file: {backup_file}")
    
    # Write updated JSON
    write_json_file(json_file, updated_data)
    print(f"Updated {json_file} with changes from {input_file}")

def extract_all(data_dir, output_dir, language):
    """Extract all types of data for the specified language."""
    os.makedirs(output_dir, exist_ok=True)
    
    file_types = ['pages', 'products', 'segments', 'team', 'team_products']
    for file_type in file_types:
        extract_to_file(data_dir, output_dir, file_type, language)

def main():
    parser = argparse.ArgumentParser(description='Extract or update JSON data for review.')
    subparsers = parser.add_subparsers(dest='command', help='Command to execute')
    
    # Extract command
    extract_parser = subparsers.add_parser('extract', help='Extract data from JSON files')
    extract_parser.add_argument('language', choices=['en', 'he'], help='Language to extract')
    extract_parser.add_argument('--data-dir', default='./pi_website_take5/data', help='Directory containing JSON files')
    extract_parser.add_argument('--output-dir', default='./extracted', help='Directory for output text files')
    extract_parser.add_argument('--type', choices=['pages', 'products', 'segments', 'team', 'team_products', 'all'], 
                               default='all', help='Type of data to extract')
    
    # Update command
    update_parser = subparsers.add_parser('update', help='Update JSON files from text files')
    update_parser.add_argument('language', choices=['en', 'he'], help='Language to update')
    update_parser.add_argument('--data-dir', default='./pi_website_take5/data', help='Directory containing JSON files')
    update_parser.add_argument('--input-file', required=True, help='Input text file with updates')
    update_parser.add_argument('--type', choices=['pages', 'products', 'segments', 'team', 'team_products'], 
                              required=True, help='Type of data to update')
    
    args = parser.parse_args()
    
    if args.command == 'extract':
        if args.type == 'all':
            extract_all(args.data_dir, args.output_dir, args.language)
        else:
            extract_to_file(args.data_dir, args.output_dir, args.type, args.language)
    
    elif args.command == 'update':
        update_from_file(args.data_dir, args.input_file, args.type, args.language)
    
    else:
        parser.print_help()

if __name__ == "__main__":
    main() 