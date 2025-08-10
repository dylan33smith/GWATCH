#!/bin/bash

# Clear all Module databases
echo "Clearing all Module databases..."

# Drop databases with proper escaping
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS \`Module_test-upload-1_20250809195424\`;"
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS \`Module_test-upload-1_20250809200448\`;"
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS \`Module_test-upload-1_20250809202915\`;"
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS \`Module_test-upload-1_20250809203116\`;"
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS \`Module_test-upload-1_20250809203340\`;"
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS \`Module_test-upload-1_20250809203430\`;"
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS \`Module_test-upload-1_20250810123233\`;"
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS \`gwatch_module_186\`;"

echo "Verifying all Module databases are cleared..."
php bin/console doctrine:query:sql "SHOW DATABASES LIKE '%Module%';"

echo "Done!"
