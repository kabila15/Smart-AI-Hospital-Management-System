import re
from html.parser import HTMLParser

class DivParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.stack = []
        
    def handle_starttag(self, tag, attrs):
        if tag == 'div':
            attr_dict = dict(attrs)
            div_id = attr_dict.get('id', '')
            div_class = attr_dict.get('class', '')
            self.stack.append((div_id, div_class, self.getpos()[0]))
            
    def handle_endtag(self, tag):
        if tag == 'div':
            if self.stack:
                closed = self.stack.pop()
                if closed[0] in ['nav-tabContent', 'list-dash', 'list-doc', 'list-pat', 'list-pres', 'list-app', 'adminPresTabsContent', 'adminAppTabsContent']:
                    print(f"Line {self.getpos()[0]}: Closed {closed[0]} (opened at {closed[2]})")
            
    def print_stack(self):
        print("Current stack:")
        for s in self.stack:
            print(f" - {s}")

parser = DivParser()
with open('admin-panel1.php', 'r', encoding='utf-8') as f:
    parser.feed(f.read())

print("Final stack remaining open:")
parser.print_stack()
