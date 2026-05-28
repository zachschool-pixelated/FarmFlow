import os
import re
import markdown
import subprocess

def convert():
    # Read markdown
    with open('Chapter_2_System_Architecture_and_Design.md', 'r', encoding='utf-8') as f:
        content = f.read()

    # Pre-process content to handle Mermaid blocks
    parts = re.split(r'```mermaid\n(.*?)\n```', content, flags=re.DOTALL)
    new_content = ""
    for i, part in enumerate(parts):
        if i % 2 == 1:
            # This is a mermaid diagram block
            new_content += f'<div class="mermaid">\n{part}\n</div>'
        else:
            new_content += part

    # Convert markdown to html
    html_body = markdown.markdown(new_content, extensions=['tables', 'fenced_code'])

    # Build the full HTML document with styles and Mermaid
    html_doc = f"""<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chapter 2: System Architecture and Design</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {{
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #334155;
            line-height: 1.6;
            margin: 40px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            font-size: 14px;
        }}
        h1, h2, h3, h4, h5, h6 {{
            color: #0f172a;
            font-weight: 600;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }}
        h1 {{
            font-size: 2.2em;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-top: 0;
        }}
        h2 {{
            font-size: 1.6em;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            page-break-before: always;
        }}
        h3 {{
            font-size: 1.25em;
        }}
        h4 {{
            font-size: 1.1em;
            color: #475569;
        }}
        p {{
            margin-top: 0;
            margin-bottom: 1.2em;
            text-align: justify;
        }}
        hr {{
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 2em 0;
        }}
        table {{
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2em;
            page-break-inside: avoid;
        }}
        th, td {{
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            text-align: left;
        }}
        th {{
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: 600;
        }}
        tr:nth-child(even) {{
            background-color: #f8fafc;
        }}
        code {{
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.9em;
        }}
        pre {{
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            margin-bottom: 1.5em;
        }}
        pre code {{
            background-color: transparent;
            padding: 0;
            font-size: 0.85em;
        }}
        .mermaid {{
            display: flex;
            justify-content: center;
            margin: 2em 0;
            page-break-inside: avoid;
        }}
        /* Page styling for printing */
        @page {{
            size: A4;
            margin: 20mm;
        }}
        @media print {{
            body {{
                margin: 0;
            }}
            .no-print {{
                display: none;
            }}
        }}
    </style>
    <!-- Include Mermaid from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({{
            startOnLoad: true,
            theme: 'default',
            securityLevel: 'loose'
        }});
    </script>
</head>
<body>
    {html_body}
</body>
</html>
"""

    # We need to write to an absolute path for Chrome to open it properly
    html_path = os.path.abspath('chapter2.html')
    with open(html_path, 'w', encoding='utf-8') as f:
        f.write(html_doc)

    print("HTML compiled successfully. Launching Chrome to print to PDF...")
    
    # Chrome command
    chrome_path = "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe"
    output_pdf = os.path.abspath("Chapter_2_System_Architecture_and_Design.pdf")
    
    cmd = [
        chrome_path,
        "--headless",
        "--disable-gpu",
        f"--print-to-pdf={output_pdf}",
        "--no-margins",
        "--virtual-time-budget=8000",
        "--run-all-compositor-stages-before-draw",
        html_path
    ]
    
    subprocess.run(cmd, check=True)
    print(f"PDF generated successfully at {output_pdf}")

if __name__ == '__main__':
    convert()
