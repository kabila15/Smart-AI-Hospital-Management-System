import re

with open('admin-panel1.php', 'r', encoding='utf-8') as f:
    html = f.read()

depth = 0
for i, line in enumerate(html.split('\n')):
    line_num = i + 1
    opens = len(re.findall(r'<div\b[^>]*>', line))
    closes = len(re.findall(r'</div\s*>', line))
    
    if opens > 0 or closes > 0:
        depth += opens
        depth -= closes
        if line_num >= 244 and line_num <= 800:
            if 'id="nav-tabContent"' in line:
                print(f'{line_num}: START nav-tabContent (depth: {depth})')
            elif depth <= 2: 
                print(f'{line_num}: depth {depth} -> {line.strip()}')
