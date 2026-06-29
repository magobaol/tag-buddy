# tag-buddy install: copy the app body to ~/.local/opt and the wrapper to ~/.local/bin.
PREFIX ?= $(HOME)/.local
OPT    := $(PREFIX)/opt/tag-buddy
BINDIR := $(PREFIX)/bin

.PHONY: install
install:
	rm -rf "$(OPT)"
	mkdir -p "$(OPT)" "$(OPT)/var" "$(BINDIR)"
	cp -R bin config public src composer.json composer.lock symfony.lock .env "$(OPT)/"
	cd "$(OPT)" && APP_ENV=prod composer install --no-dev --no-interaction --optimize-autoloader
	install -m 0755 install/tag-buddy "$(BINDIR)/tag-buddy"
	@echo "installed: $(BINDIR)/tag-buddy -> $(OPT)"
