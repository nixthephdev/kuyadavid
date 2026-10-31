// Creates an empty SQLite file for the browser tests.
import fs from 'node:fs';

fs.writeFileSync('database/e2e.sqlite', '');
