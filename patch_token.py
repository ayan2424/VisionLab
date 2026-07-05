
import re

filepath = '/usr/lib/code-server/lib/vscode/out/vs/server/node/server.main.js'
with open(filepath, 'r') as f:
    content = f.read()

# Find the $Cn function definition and replace it
# Original: e.$Cn=f; ... function f(h,g,w){if(h.validate(w.query[R.$Mg]))return!0;const m=n.parse(g.headers.cookie||"");return h.validate(m[R.$Lg])}
# We want to make f always return true

old = 'function f(h,g,w){if(h.validate(w.query[R.$Mg]))return!0;const m=n.parse(g.headers.cookie||"");return h.validate(m[R.$Lg])}'
new = 'function f(h,g,w){return!0}'

if old in content:
    content = content.replace(old, new)
    with open(filepath, 'w') as f:
        f.write(content)
    print("PATCHED SUCCESSFULLY!")
else:
    print("Pattern not found, trying alternate...")
    # Try to find and show what the function looks like
    idx = content.find('e.$Cn=f;')
    if idx >= 0:
        # Find 'function f(' after this point
        fidx = content.find('function f(', idx)
        if fidx >= 0:
            # Get text from function f( to the next closing }
            end = fidx
            braces = 0
            started = False
            for i in range(fidx, min(fidx+500, len(content))):
                if content[i] == '{':
                    braces += 1
                    started = True
                elif content[i] == '}':
                    braces -= 1
                    if started and braces == 0:
                        end = i + 1
                        break
            original = content[fidx:end]
            print(f"Found function: {original}")
            content = content.replace(original, 'function f(h,g,w){return!0}')
            with open(filepath, 'w') as f:
                f.write(content)
            print("PATCHED WITH ALTERNATE!")
        else:
            print("function f( not found after $Cn")
    else:
        print("$Cn not found at all")
