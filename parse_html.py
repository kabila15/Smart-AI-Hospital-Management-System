from html.parser import HTMLParser

class StructureParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.path = []
        self.list_doc = None
        self.list_app = None

    def handle_starttag(self, tag, attrs):
        attr_dict = dict(attrs)
        tag_id = attr_dict.get('id', '')
        tag_class = attr_dict.get('class', '')
        node = f"{tag}#{tag_id}" if tag_id else tag
        self.path.append(node)
        
        if tag_id == 'list-doc':
            self.list_doc = list(self.path)
        if tag_id == 'list-app':
            self.list_app = list(self.path)
            
        if tag_id == 'nav-tabContent':
            self.nav_tab_content = list(self.path)

    def handle_endtag(self, tag):
        if self.path and self.path[-1].startswith(tag):
            self.path.pop()
        else:
            # find the last matching tag and pop up to it
            for i in range(len(self.path)-1, -1, -1):
                if self.path[i].startswith(tag):
                    self.path = self.path[:i]
                    break

parser = StructureParser()
with open('admin-panel1.php', 'r', encoding='utf-8') as f:
    parser.feed(f.read())

print("nav-tabContent path:", parser.nav_tab_content)
print("list-doc path:", parser.list_doc)
print("list-app path:", parser.list_app)
