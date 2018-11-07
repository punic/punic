# You can set these variables from the command line.
SPHINXOPTS =
SPHINXBUILD = sphinx-build
SOURCEDIR = src
BUILDDIR = out

help:
	@$(SPHINXBUILD) -M help '$(SOURCEDIR)' '$(BUILDDIR)' $(SPHINXOPTS) $(O)

.PHONY: help Makefile

clean:
	@rm -rf '$(BUILDDIR)'

%: Makefile
	@$(SPHINXBUILD) -M $@ '$(SOURCEDIR)' '$(BUILDDIR)' $(SPHINXOPTS) $(O)
