import os
import re

guide_dir = r"c:\xampp\htdocs\CeylonGo\views\guide"
transport_dir = r"c:\xampp\htdocs\CeylonGo\views\transport"

def fix_sidebar(content, is_guide=True):
    # Remove My Places from Guide
    if is_guide:
        content = re.sub(r'\s*<li><a href="/CeylonGo/public/guide/places">.*?</li>', '', content)
    
    # Reorder sidebar: move Performance Report to bottom
    # We look for the whole <ul> block or a sequence of <li> items
    report_pattern = r'(\s*<li[^>]*><a href="[^"]*report">.*?</li>)'
    payment_pattern = r'(\s*<li[^>]*><a href="[^"]*payment">.*?</li>)'
    
    # If both exist and report is before payment, swap them
    search_pattern = report_pattern + r'(\s*)' + payment_pattern
    if re.search(search_pattern, content, re.DOTALL):
        content = re.sub(search_pattern, r'\3\2\1', content, flags=re.DOTALL)
    
    return content

def fix_transport_report(content):
    # Ensure generatedAt exists
    if 'generatedAt =' not in content:
        content = content.replace('// Period label', '$generatedAt = date("F d, Y \\a\\t h:i A");\n// Period label')
    
    # Add brand header if not there
    if 'report-brand-header' not in content:
        brand_html = """
            <!-- Report Branding Header (hidden on screen, visible in print/PDF) -->
            <div class="report-brand-header" id="reportBrandHeader">
                <div class="brand-left">
                    <img src="/CeylonGo/public/images/logo.png" alt="Ceylon Go" class="brand-logo">
                    <div class="brand-text">
                        <h1>Ceylon Go</h1>
                        <span class="brand-tagline">Transport Performance Report</span>
                    </div>
                </div>
                <div class="brand-right">
                    <div class="brand-info-row"><i class="fa-regular fa-user"></i> <strong><?= htmlspecialchars($user_name) ?></strong></div>
                    <div class="brand-info-row"><i class="fa-solid fa-id-badge"></i> Transport Provider</div>
                    <div class="brand-info-row"><i class="fa-regular fa-clock"></i> Generated: <?= $generatedAt ?></div>
                    <div class="brand-info-row"><i class="fa-regular fa-calendar-check"></i> Period: <?= htmlspecialchars($periodLabel) ?></div>
                </div>
            </div>"""
        content = content.replace('<div class="main-content" id="reportContent">', '<div class="main-content" id="reportContent">\n' + brand_html)

    return content

# I'll focus on the sidebar fix for all files first to clean up the mess.
all_files = []
for root, dirs, files in os.walk(guide_dir):
    for f in files:
        if f.endswith('.php'):
            all_files.append((os.path.join(root, f), True))
for root, dirs, files in os.walk(transport_dir):
    for f in files:
        if f.endswith('.php'):
            all_files.append((os.path.join(root, f), False))

for file_path, is_guide in all_files:
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        new_content = fix_sidebar(content, is_guide)
        
        # Cleanup any literal `n or mess left by the previous powershell script
        new_content = new_content.replace('`n', '\n')
        
        if new_content != content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Fixed sidebar: {file_path}")
    except Exception as e:
        print(f"Error on {file_path}: {e}")
