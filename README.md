# Contact-task
A Contact formon a felhasználók kérdezni tudnak az oldaltól.
Az admin szekció tartalmaz egy lapozható listát, az összes beküldött üzenettel. Beküldött üzenetek DTO-n keresztül Ticket entitássá válnak.
Készítettem egy előre inicializált felhasználót „admin” felhasználónévvel és „password” jelszóval.
A felhasználóknak is van egy lista oldala, ahol szabadon tudunk admin felhasználókat felvenni és módosítani.

# Tech stack
<ol>
    <li>Symfony 7</li>
    <li>bootstrap 5</li>
</ol>

# Start
<ol>
    <li>git clone https://github.com/mezeigergely/contact-task.git</li>
    <li>cd contact-task</li>
    <li>composer install</li>
    <li>symfony console doctrine:database:create</li>
    <li>symfony console doctrine:migrations:migrate</li>
    <li>symfony console doctrine:fixtures:load</li>
    <li>npm install</li>
    <li>npm run watch</li>
</ol>
