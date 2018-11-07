# -*- coding: utf-8 -*-
#
# Configuration file for the Sphinx documentation builder.
# http://www.sphinx-doc.org/en/master/config

# -- Custom functions --------------------------------------------------------

import os, datetime, re, subprocess

def get_punic_rootdir():
    return os.path.dirname(os.path.dirname(os.path.dirname(os.path.realpath(__file__))))

def get_punic_copyright():
    initialYear = 2018
    result = str(initialYear)
    currentYear = datetime.datetime.now().year
    if currentYear <> initialYear:
        result += u'-' + str(currentYear)
    result += u', Michele Locati'
    return result

def get_punic_version(full):
    if get_punic_version.fullVersion:
        fullVersion = get_punic_version.fullVersion
    else:
        isDev = False
        fullVersion = False
        rxVersion = re.compile(u'^### (\d+\.\d+\.\d+)')
        with open(os.path.join(get_punic_rootdir(), 'CHANGELOG.md')) as file:
            for line in file:
                line = line.strip()
                if line == '### NEXT (YYYY-MM-DD)':
                    isDev = True
                else:
                    m = rxVersion.match(line)
                    if m:
                        fullVersion = m.group(1)
                        break
        if not fullVersion:
            raise Exception(u'Failed to detect the version from the CHANGELOG file.')
        if isDev:
            numbers = fullVersion.split('.')
            numbers[2] = str(1 + int(numbers[2]))
            fullVersion = '.'.join(numbers) + '-dev'
        get_punic_version.fullVersion = fullVersion
    if full:
        return fullVersion
    return re.match(u'^(\d+\.\d+)', fullVersion).group(1)
get_punic_version.fullVersion = False

# -- Configure lexers --------------------------------------------------------

from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer
from pygments.lexers.data import JsonBareObjectLexer
lexers['php'] = PhpLexer(startinline=True, linenos=1)
lexers['php-annotations'] = PhpLexer(startinline=True, linenos=1)
lexers['json-chunk'] = JsonBareObjectLexer()


# -- Links to source code ----------------------------------------------------

html_copy_source = False

html_context = {
  'display_github': True,
  'github_user': 'punic',
  'github_repo': 'punic',
  'github_version': 'master',
  'conf_py_path': '/docs/src/',
  'source_suffix': '.rst',
}

# -- Project information -----------------------------------------------------

project = u'Punic'
copyright = get_punic_copyright()
author = u'Michele Locati'

# The short X.Y version
version = get_punic_version(False)
# The full version, including alpha/beta/rc tags
release = get_punic_version(True)

# -- General configuration ---------------------------------------------------

html_favicon = '../static/favicon.ico'

# If your documentation needs a minimal Sphinx version, state it here.
#
# needs_sphinx = '1.0'

# Add any Sphinx extension module names here, as strings. They can be
# extensions coming with Sphinx (named 'sphinx.ext.*') or your custom
# ones.
extensions = [
]

# Add any paths that contain templates here, relative to this directory.
templates_path = ['ntemplates']

# The suffix(es) of source filenames.
# You can specify multiple suffix as a list of string:
#
# source_suffix = ['.rst', '.md']
source_suffix = '.rst'

# The master toctree document.
master_doc = 'index'

# The language for content autogenerated by Sphinx. Refer to documentation
# for a list of supported languages.
#
# This is also used if you do content translation via gettext catalogs.
# Usually you set "language" from the command line for these cases.
language = None

# List of patterns, relative to source directory, that match files and
# directories to ignore when looking for source files.
# This pattern also affects html_static_path and html_extra_path.
exclude_patterns = [u'out', 'Thumbs.db', '.DS_Store']

# The name of the Pygments (syntax highlighting) style to use.
pygments_style = None


# -- Options for HTML output -------------------------------------------------

html_theme_path = [
    '../themes',
]
# The theme to use for HTML and HTML Help pages.  See the documentation for
# a list of builtin themes.
#
html_theme = 'punic'

# Theme options are theme-specific and customize the look and feel of a theme
# further.  For a list of options available for each theme, see the
# documentation.
#
html_theme_options = {
}

# Add any paths that contain custom static files (such as style sheets) here,
# relative to this directory. They are copied after the builtin static files,
# so a file named "default.css" will overwrite the builtin "default.css".
html_static_path = ['../static']

# Custom sidebar templates, must be a dictionary that maps document names
# to template names.
#
# The default sidebars (for documents that don't match any pattern) are
# defined by theme itself.  Builtin themes are using these templates by
# default: ``['localtoc.html', 'relations.html', 'sourcelink.html',
# 'searchbox.html']``.
#
# html_sidebars = {}


# -- Options for HTMLHelp output ---------------------------------------------

# Output file base name for HTML help builder.
htmlhelp_basename = 'PunicDoc'


# -- Options for LaTeX output ------------------------------------------------

latex_elements = {
    # The paper size ('letterpaper' or 'a4paper').
    #
    # 'papersize': 'letterpaper',

    # The font size ('10pt', '11pt' or '12pt').
    #
    # 'pointsize': '10pt',

    # Additional stuff for the LaTeX preamble.
    #
    # 'preamble': '',

    # Latex figure (float) alignment
    #
    # 'figure_align': 'htbp',
}

# Grouping the document tree into LaTeX files. List of tuples
# (source start file, target name, title,
#  author, documentclass [howto, manual, or own class]).
latex_documents = [
    (master_doc, 'Punic.tex', u'Punic Documentation',
     u'Michele Locati', 'manual'),
]


# -- Options for manual page output ------------------------------------------

# One entry per manual page. List of tuples
# (source start file, name, description, authors, manual section).
man_pages = [
    (master_doc, 'punic', u'Punic Documentation',
     [author], 1)
]


# -- Options for Texinfo output ----------------------------------------------

# Grouping the document tree into Texinfo files. List of tuples
# (source start file, target name, title, author,
#  dir menu entry, description, category)
texinfo_documents = [
    (master_doc, 'Punic', u'Punic Documentation',
     author, 'Punic', 'One line description of project.',
     'Miscellaneous'),
]


# -- Options for Epub output -------------------------------------------------

# Bibliographic Dublin Core info.
epub_title = project

# The unique identifier of the text. This can be a ISBN number
# or the project homepage.
#
# epub_identifier = ''

# A unique identification for the text.
#
# epub_uid = ''

# A list of files that should not be packed into the epub file.
epub_exclude_files = ['search.html']


# -- Extension configuration -------------------------------------------------