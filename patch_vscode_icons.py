import os
import shutil

src_icon = r"public\icons\logo-orange.svg"

targets = [
    r"visionlab-ide\lib\vscode\src\vs\workbench\browser\parts\editor\media\letterpress-dark.svg",
    r"visionlab-ide\lib\vscode\src\vs\workbench\browser\parts\editor\media\letterpress-hcDark.svg",
    r"visionlab-ide\lib\vscode\src\vs\workbench\browser\parts\editor\media\letterpress-hcLight.svg",
    r"visionlab-ide\lib\vscode\src\vs\workbench\browser\parts\editor\media\letterpress-light.svg",
]

for t in targets:
    if os.path.exists(t):
        shutil.copy(src_icon, t)
        print(f"Replaced {t}")
    else:
        print(f"Not found: {t}")

print("Replacing Default Themes to enforce Dark Mode...")
theme_file = r"visionlab-ide\lib\vscode\src\vs\workbench\services\themes\common\workbenchThemeService.ts"

if os.path.exists(theme_file):
    with open(theme_file, "r", encoding="utf-8") as f:
        content = f.read()

    # Change COLOR_THEME_LIGHT to default to Dark Modern
    content = content.replace(
        "COLOR_THEME_LIGHT = 'Default Light Modern'",
        "COLOR_THEME_LIGHT = 'Default Dark Modern'"
    )
    content = content.replace(
        "COLOR_THEME_LIGHT_OLD = 'Default Light+'",
        "COLOR_THEME_LIGHT_OLD = 'Default Dark+'"
    )

    with open(theme_file, "w", encoding="utf-8") as f:
        f.write(content)
    print("Theme defaults updated.")
else:
    print(f"Theme file not found: {theme_file}")

# Disable Copilot by blocking it in product.json built-in extensions (if any) or adding a block.
# Actually, the user's issue with copilot might be because it's not disabled by default or students install it.
# Wait, product.json doesn't have it. We'll just patch CodeServerManager later to prevent its installation.
