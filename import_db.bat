@echo off
setlocal enabledelayedexpansion
cd /d "f:\belajar komputer\sinau php\perpustakaan_ukk"
"C:\xampp\mysql\bin\mysql.exe" -u root < database.sql
echo Database imported successfully!
pause
