# lazarbojicic15422php2sajt


Instalacija

git clone https://github.com/LazarBojicic1/lazarbojicic15422php2sajt cd lazarbojicic15422php2sajt

composer install
npm install

cp .env.example .env   //Koristiti mysql bazu

php artisan key:generate
php artisan storage:link

php artisan migrate:fresh --seed 
php artisan tmdb:import 
php artisan db:seed  //pokrecemo ponovo nakon importovanih serija i filmova

npm run dev 
php artisan serve


Kredencijali

Admin:
admin@example.com / admin12345

Moderator:
moderator@example.com / moderator12345

Korisnik:
user@example.com / user12345