import re

with open('dashboard_raw.html', 'r', encoding='utf-8') as f:
    html = f.read()

# Extract the content of the dashboard
match = re.search(r'<!-- Contenido del Dashboard -->(.*?)(?=</main>)', html, re.DOTALL)
if match:
    content = match.group(1).strip()
    
    # Replace static metrics with Blade variables
    content = re.sub(r'>\$24,850\.00<', r'>${{ number_format($ventasTotales, 2) }}<', content)
    content = re.sub(r'>142<', r'>{{ $totalFacturas }}<', content)
    content = re.sub(r'>28<', r'>{{ $nuevosClientes }}<', content)
    content = re.sub(r'>\$175\.00<', r'>${{ number_format($ticketPromedio, 2) }}<', content)
    
    with open('resources/views/livewire/dashboard.blade.php', 'w', encoding='utf-8') as out:
        out.write('<div>\n' + content + '\n</div>')
    print('Extracted successfully')
else:
    print('Not found')
