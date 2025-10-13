@echo off
echo Starting Laravel and Reverb servers...
start cmd /k "cd /d G:\Kuliah\SEMESTER 8\AplikasiSkripsi\laparchicken_inventory_and_sales && php artisan serve"
start cmd /k "cd /d G:\Kuliah\SEMESTER 8\AplikasiSkripsi\laparchicken_inventory_and_sales && php artisan reverb:start"
echo Both servers started!
