import os
import glob
import re

directories_to_search = [
    'resources/views/**/*.blade.php',
]

files = []
for pattern in directories_to_search:
    files.extend(glob.glob(pattern, recursive=True))

for filepath in files:
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
    except Exception:
        continue
    
    original = content
    
    def add_bg(match):
        attrs = match.group(0)
        if 'class="' in attrs or "class='" in attrs:
            if 'dark:bg-gray-900' not in attrs:
                # Add it right after border-gray-300 or dark:border-gray-600
                attrs = attrs.replace('dark:border-gray-600', 'dark:border-gray-600 dark:bg-gray-900 dark:text-white')
                if 'dark:bg-gray-900' not in attrs:
                   attrs = attrs.replace('border-gray-300', 'border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white')
        return attrs

    content = re.sub(r'<(select|input|textarea)\b[^>]+>', add_bg, content)
    
    # Clean up any potential duplicates created just in case
    content = content.replace('dark:bg-gray-900 dark:bg-gray-900', 'dark:bg-gray-900')
    content = content.replace('dark:text-white dark:text-white', 'dark:text-white')
    content = content.replace('dark:border-gray-600 dark:border-gray-600', 'dark:border-gray-600')

    if original != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f'Updated {filepath}')

print('Done updating inputs.')
