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
        if line_num >= 470 and line_num <= 650:
            print(f'{line_num}: +{opens} -{closes} -> depth {depth} | {line.strip()}')
