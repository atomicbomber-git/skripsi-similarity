# Skripsi Similarity

## Requirements

a) Postgresql & Extensions like `smlar` and `pg_trgm`

### PostgreSQL
1) The `smlar` extension
```bash
# Install prerequisites for compiling smlar
sudo apt-get install postgresql-server-dev-12

# Compile and install smlar
git clone https://github.com/jirutka/smlar
cd smlar
sudo make install USE_PGXS=1
```
```postgresql
-- Add to your database after logging in
CREATE EXTENSION smlar;
```