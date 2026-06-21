import os
import glob
import re

css_path = r'c:\Users\ayans\OneDrive\Documents\A Projects\Aptech\Vision2026\VisionLab\resources\css\app.css'
js_path = r'c:\Users\ayans\OneDrive\Documents\A Projects\Aptech\Vision2026\VisionLab\resources\js\app.js'

# Update app.css
try:
    with open(css_path, 'r', encoding='utf-8') as f:
        css_content = f.read()

    # Extract the dark theme variables
    dark_match = re.search(r'\.dark\s*\{([^\}]+)\}', css_content)
    if dark_match:
        dark_vars = dark_match.group(1).strip()
        # Replace the :root block with dark vars
        css_content = re.sub(r':root\s*\{([^\}]+)\}', f":root {{\n    {dark_vars}\n}}", css_content)
        # Remove the .dark block entirely
        css_content = re.sub(r'/\*\s*── Dark Theme[^\*]+\*/\s*\.dark\s*\{[^\}]+\}', '', css_content)
        # Remove theme toggle css
        css_content = re.sub(r'/\*\s*── Theme toggle button[^\*]+\*/\s*\.theme-toggle\s*\{[^\}]+\}\s*\.theme-toggle:hover\s*\{[^\}]+\}', '', css_content)

        with open(css_path, 'w', encoding='utf-8') as f:
            f.write(css_content)
        print("Updated app.css")
except Exception as e:
    print(f"Error css: {e}")

# Update app.js
try:
    with open(js_path, 'w', encoding='utf-8') as f:
        f.write("""import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Force dark mode
document.documentElement.classList.add('dark');

Alpine.start();
""")
    print("Updated app.js")
except Exception as e:
    print(f"Error js: {e}")

# Now clean blade files
directories = [r'c:\Users\ayans\OneDrive\Documents\A Projects\Aptech\Vision2026\VisionLab\resources\views\**\*.blade.php']

files = []
for d in directories:
    files.extend(glob.glob(d, recursive=True))

for filepath in files:
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
            
        original = content
        
        # Remove theme-toggle buttons
        content = re.sub(r'<button[^>]*theme-toggle[^>]*>[\s\S]*?</button>', '', content)
        content = re.sub(r'<button[^>]*onclick="window\.themeManager\.toggle\(\)"[^>]*>[\s\S]*?</button>', '', content)
        content = re.sub(r'<button[^>]*toggleTheme\(\)[^>]*>[\s\S]*?</button>', '', content)
        
        # Remove script tags that set vc-theme
        content = re.sub(r'<script>\s*\(\s*function\(\)\s*\{[^\}]*localStorage\.setItem\(\'vc-theme\'[^\}]*\}\)\(\);\s*</script>', '', content)
        content = re.sub(r'var\s+t\s*=\s*localStorage\.getItem\(\'vc-theme\'\);[\s\S]*?\}', '', content)
        content = re.sub(r'localStorage\.setItem\(\'vc-theme\'[^;]+;', '', content)
        
        # Remove toggleTheme function
        content = re.sub(r'function\s+toggleTheme\(\)\s*\{[\s\S]*?\}', '', content)
        
        # Remove svgs specifically for theme toggle
        content = re.sub(r'<svg\s+id="theme-icon-(?:dark|light)"[\s\S]*?</svg>', '', content)
        
        # In case there's any stray {{-- Theme toggle --}} comments
        content = re.sub(r'\{\{--\s*Theme toggle\s*[^\}]*--\}\}', '', content)
        
        if original != content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Updated {filepath}")
            
    except Exception as e:
        print(f"Error {filepath}: {e}")
