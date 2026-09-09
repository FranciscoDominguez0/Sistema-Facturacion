import re

filepath = 'resources/views/layouts/app.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    html = f.read()

# Fix dashboard link path
html = html.replace(
    '''@click="currentPath = '/'"''',
    '''@click="currentPath = '/dashboard'"'''
)
html = html.replace(
    '''currentPath === '/' ''',
    '''currentPath === '/dashboard' '''
)

# Remove @persist('topbar')
html = html.replace(
    '''        @persist('topbar')\n        <!-- TopNavBar -->''',
    '''        <!-- TopNavBar -->'''
)
html = html.replace(
    '''        </header>\n        @endpersist''',
    '''        </header>'''
)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(html)
