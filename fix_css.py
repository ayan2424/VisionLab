import os
import re
import glob

# The directory containing the views
views_dir = r"C:\Users\ayans\OneDrive\Documents\A_Projects\Aptech\Vision2026\VisionLab\resources\views\admin\erp"

def fix_view(file_path):
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()

    # 1. Fix the layout header
    # Replace @section('header') to @endsection with @section('title', '...') @section('page-title', '...')
    # But only if it exists.
    header_match = re.search(r"@section\('header'\)\s*<h2[^>]*>\s*(.*?)\s*</h2>\s*@endsection", content, re.DOTALL)
    title = "ERP Section"
    if header_match:
        raw_title = header_match.group(1).strip()
        # Remove {{ __(' and ') }}
        raw_title = raw_title.replace("{{ __('", "").replace("') }}", "")
        title = raw_title
        content = content[:header_match.start()] + f"@section('title', '{title}')\n@section('page-title', '{title}')" + content[header_match.end():]

    # 2. Simplify the wrapper divs
    # Remove <div class="py-12"> <div class="max-w-7xl..."> <div class="bg-white..."> <div class="p-6...">
    # And replace with <div class="vc-card overflow-hidden">
    # This is tricky with regex, so we'll do literal replacements if possible,
    # or just replace the common opening sequence.
    wrapper_pattern = re.compile(r'<div class="py-12">\s*<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">\s*<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">\s*<div class="p-6 text-gray-900">', re.DOTALL)
    
    if wrapper_pattern.search(content):
        content = wrapper_pattern.sub(r'<div class="vc-card overflow-hidden p-6">', content)
        # Also we need to remove the corresponding closing divs at the end of @section('content')
        content = content.replace("</div>\n            </div>\n        </div>\n    </div>\n</div>\n@endsection", "</div>\n@endsection")
        content = content.replace("</div>\n        </div>\n    </div>\n</div>\n@endsection", "</div>\n@endsection")

    # 3. Replace common Tailwind table classes
    content = content.replace('class="min-w-full divide-y divide-gray-200"', 'class="w-full text-left border-collapse"')
    content = content.replace('<thead class="bg-gray-50">', '<thead>')
    content = content.replace('class="bg-white divide-y divide-gray-200"', '')
    content = content.replace('class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"', 'class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);"')
    content = content.replace('<tr>\n                                    <td class="px-6', '<tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors border-b" style="border-color:var(--vc-border);">\n                                    <td class="px-5')
    content = content.replace('class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"', 'class="px-5 py-4" style="color:var(--vc-text); font-weight:600;"')
    content = content.replace('class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"', 'class="px-5 py-4 text-sm" style="color:var(--vc-muted);"')
    content = content.replace('class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"', 'class="px-5 py-4 text-right text-sm font-medium"')

    # 4. Buttons
    content = content.replace('class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700"', 'class="btn-primary py-2 px-4 text-sm"')
    content = content.replace('class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700"', 'class="btn-ghost py-2 px-4 text-sm"')
    content = content.replace('class="text-indigo-600 hover:text-indigo-900"', 'class="btn-ghost py-1 px-3 text-xs"')
    content = content.replace('class="text-red-600 hover:text-red-900 ml-4"', 'class="btn-ghost py-1 px-3 text-xs text-red-500"')

    # 5. Headers inside content
    content = re.sub(r'<h3 class="text-lg font-medium.*?>(.*?)</h3>', r'<h1 class="text-xl font-bold mb-4" style="color:var(--vc-text);">\1</h1>', content)

    # 6. Forms
    content = re.sub(r'class="mt-1 block w-full border-gray-300 rounded-md shadow-sm.*?"', 'class="vc-input w-full"', content)
    content = re.sub(r'class="block text-sm font-medium text-gray-700"', 'class="block text-sm font-bold mb-2" style="color:var(--vc-text);"', content)
    
    # Text colors
    content = content.replace('text-gray-900', '')
    content = content.replace('text-gray-800', '')
    
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)

for root, dirs, files in os.walk(views_dir):
    for file in files:
        if file.endswith(".blade.php"):
            fix_view(os.path.join(root, file))
            print(f"Fixed {file}")
