"""
Wraps all bare <table> tags (not already inside a .table-responsive div)
with <div class="table-responsive"> in the specified PHP files.
"""
import re

files = [
    'admin-panel.php',
    'admin-panel1.php',
    'doctor-panel.php',
]

for fname in files:
    with open(fname, 'r', encoding='utf-8') as f:
        content = f.read()

    original = content

    # Find tables NOT already wrapped in table-responsive
    # We'll use a simple approach: replace <table that is not preceded by table-responsive
    # Pattern: match a line with <table (not already inside table-responsive)
    # We track if the previous non-empty line contains "table-responsive"

    lines = content.split('\n')
    result = []
    i = 0
    changes = 0
    while i < len(lines):
        line = lines[i]
        stripped = line.strip()
        
        # Check if this line opens a table
        if re.match(r'^\s*<table\b', line, re.IGNORECASE):
            # Check if prev non-empty line is already a table-responsive wrapper
            prev_lines = [l.strip() for l in result if l.strip()]
            prev = prev_lines[-1] if prev_lines else ''
            
            if 'table-responsive' not in prev:
                indent = re.match(r'^(\s*)', line).group(1)
                result.append(f'{indent}<div class="table-responsive">')
                result.append(line)
                
                # Now find the matching </table> and close the wrapper
                depth = 1
                i += 1
                while i < len(lines) and depth > 0:
                    l = lines[i]
                    if re.search(r'<table\b', l, re.IGNORECASE):
                        depth += 1
                    if re.search(r'</table\s*>', l, re.IGNORECASE):
                        depth -= 1
                    result.append(l)
                    i += 1
                result.append(f'{indent}</div><!-- /table-responsive -->')
                changes += 1
                continue
        
        result.append(line)
        i += 1

    new_content = '\n'.join(result)
    
    if new_content != original:
        with open(fname, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f'{fname}: {changes} table(s) wrapped')
    else:
        print(f'{fname}: no changes needed')
