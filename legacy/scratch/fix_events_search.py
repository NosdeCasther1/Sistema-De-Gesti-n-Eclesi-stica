import re
import os

path = r'c:\xampp\htdocs\ProyectoIglesia\Vistas\html\MostrarEventos.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace HTML
html_pattern = r'(<div class="input-group">)(\s*)(<span class="input-group-text bg-white border-end-0 text-muted"><i\s*class="fas fa-search"></i></span>)(\s*)(<input type="text" class="form-control border-start-0 ps-0 search-input"\s*placeholder="Buscar por Nombre del Evento o Lugar..."\s*style="border-radius: 0 8px 8px 0; box-shadow: none;">)(\s*)(</div>)'
html_replacement = r'<div class="input-group search-group">\2\3\4<input type="text" class="form-control border-start-0 border-end-0 ps-0 search-input" id="searchInput" placeholder="Buscar por Nombre del Evento o Lugar..." style="box-shadow: none;">\6<button class="btn bg-white border-start-0 border text-muted clear-search" type="button" id="clearSearch" style="display: none;"><i class="fas fa-times"></i></button>\6</div>'

new_content = re.sub(html_pattern, html_replacement, content)

# Replace JS
js_pattern = r"(\$searchInput\.on\('keyup', function \(\) \{)(.*?)(\}\);)(\s*)(\$searchInput\.on\('search', function \(\) \{)(.*?)(\}\);)"
js_replacement = r"""$searchInput.on('input', function () {
            const clearBtn = $('#clearSearch');
            if ($(this).val().length > 0) {
                clearBtn.show();
            } else {
                clearBtn.hide();
            }

            const searchTerm = $(this).val().toLowerCase();
            $('.table-softwys tbody tr').each(function () {
                const $row = $(this);
                const nameAndLocation = $row.find('td:eq(0)').text().toLowerCase();
                $row.toggle(nameAndLocation.includes(searchTerm));
            });
        });

        $('#clearSearch').on('click', function() {
            $searchInput.val('');
            $(this).hide();
            $('.table-softwys tbody tr').show();
            $searchInput.focus();
        });"""

new_content = re.sub(js_pattern, js_replacement, new_content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(new_content)
