# GWatch Development Makefile
# Provides easy commands for common development operations

.PHONY: help clean-modules test-upload clear-cache install-deps test-syntax check-db status


# Default target
help:
	@echo "GWatch Development Commands:"
	@echo ""
	@echo "Database Management:"
	@echo "  clean-modules     - Remove all module databases and start fresh"
	@echo "  check-db          - Check database connection and status"
	@echo "  status            - Show current application status"
	@echo ""
	@echo "Development:"
	@echo "  clear-cache       - Clear Symfony cache and logs"
	@echo "  test-syntax       - Check PHP syntax of all files"
	@echo "  install-deps      - Install/update Composer dependencies"
	@echo ""
	@echo "Testing:"
	@echo "  test-upload       - Prepare for testing module uploads"
	@echo ""
	@echo "Maintenance:"
	@echo "  help              - Show this help message"

# Database Management
clean-modules:
	@echo "🧹 Cleaning all module databases..."
	@mysql -u gwatch_user -p123457 -e "SHOW DATABASES LIKE 'Module_%';" | grep -v "Database" | xargs -I {} mysql -u gwatch_user -p123457 -e "DROP DATABASE {};" 2>/dev/null || echo "No module databases found or error occurred"
	@mysql -u gwatch_user -p123457 gwatch_db -e "DELETE FROM module_tracking WHERE id > 0;" 2>/dev/null || echo "No module tracking entries found or error occurred"
	@echo "✅ All module databases and tracking entries removed"

check-db:
	@echo "🔍 Checking database connections..."
	@echo "Main database (gwatch_db):"
	@mysql -u gwatch_user -p123457 -e "USE gwatch_db; SHOW TABLES;" 2>/dev/null || echo "❌ Main database connection failed"
	@echo ""
	@echo "Module databases:"
	@mysql -u gwatch_user -p123457 -e "SHOW DATABASES LIKE 'Module_%';" 2>/dev/null || echo "No module databases found"

status:
	@echo "📊 GWatch Application Status"
	@echo "=========================="
	@echo "PHP Version:"
	@php --version | head -1
	@echo ""
	@echo "Symfony Console:"
	@php bin/console --version 2>/dev/null || echo "❌ Symfony console not available"
	@echo ""
	@echo "Database Status:"
	@mysql -u gwatch_user -p123457 -e "SELECT COUNT(*) as 'Total Modules' FROM gwatch_db.module_tracking;" 2>/dev/null || echo "❌ Database connection failed"
	@echo ""
	@echo "Cache Status:"
	@if [ -d "var/cache" ]; then echo "✅ Cache directory exists"; else echo "❌ Cache directory missing"; fi
	@echo ""
	@echo "Upload Directory:"
	@if [ -d "data/upload_data" ]; then echo "✅ Upload data directory exists"; else echo "❌ Upload data directory missing"; fi

# Development Operations
clear-cache:
	@echo "🧹 Clearing Symfony cache and logs..."
	@php bin/console cache:clear 2>/dev/null || echo "❌ Cache clear failed"
	@rm -rf var/cache/* var/log/* 2>/dev/null || echo "❌ Manual cache cleanup failed"
	@echo "✅ Cache and logs cleared"

test-syntax:
	@echo "🔍 Checking PHP syntax..."
	@find src -name "*.php" -exec php -l {} \; | grep -v "No syntax errors detected" || echo "✅ All PHP files have valid syntax"

install-deps:
	@echo "📦 Installing/updating Composer dependencies..."
	@composer install --no-dev --optimize-autoloader
	@echo "✅ Dependencies installed"

# Testing
test-upload:
	@echo "🧪 Preparing for module upload testing..."
	@make clear-cache
	@echo "✅ Ready for testing module uploads"
	@echo ""
	@echo "Next steps:"
	@echo "1. Go to your upload page"
	@echo "2. Upload a density_X.csv file"
	@echo "3. Check the results"

# Advanced Operations
backup-modules:
	@echo "💾 Creating backup of all module databases..."
	@mkdir -p backups/$(shell date +%Y%m%d_%H%M%S)
	@for db in $$(mysql -u gwatch_user -p123457 -e "SHOW DATABASES LIKE 'Module_%';" | grep -v "Database" | tr '\n' ' '); do \
		echo "Backing up $$db..."; \
		mysqldump -u gwatch_user -p123457 "$$db" > "backups/$(shell date +%Y%m%d_%H%M%S)/$$db.sql"; \
	done
	@echo "✅ Module databases backed up"

restore-modules:
	@echo "⚠️  WARNING: This will overwrite existing module databases!"
	@echo "Latest backup found:"
	@ls -la backups/ | tail -1 || echo "No backups found"
	@echo ""
	@read -p "Enter backup directory to restore from: " backup_dir; \
	if [ -d "backups/$$backup_dir" ]; then \
		for sql_file in backups/$$backup_dir/*.sql; do \
			db_name=$$(basename "$$sql_file" .sql); \
			echo "Restoring $$db_name..."; \
			mysql -u gwatch_user -p123457 < "$$sql_file"; \
		done; \
		echo "✅ Module databases restored"; \
	else \
		echo "❌ Backup directory not found"; \
	fi

# Development Server
dev-server:
	@echo "🚀 Starting Symfony development server..."
	@php -S localhost:8000 -t public/

# Quick Reset (for development)
reset-dev:
	@echo "🔄 Quick development reset..."
	@make clean-modules
	@make clear-cache
	@echo "✅ Development environment reset complete"
