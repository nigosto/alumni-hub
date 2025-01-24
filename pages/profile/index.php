<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require_once __DIR__ . "/../../models/user.php";
require_once __DIR__ . "/../../components/button/link.php";
require_once __DIR__ . "/../../components/message/message_component.php";

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();
$message = new MessageComponent();

$user = $controller->get_user();
$user_data = $user->to_array();
$username = $user_data["username"];
$email = $user_data["email"];
$role = $_SESSION["role"];

$user_id = $_SESSION["id"];
$fn = $_SESSION["fn"];
$clothing_for_student = $controller->clothes_service->get_clothing_for_student($fn);
$clothes = $controller->clothes_service->get_clothes_with_size();

if (isset($fn)) {
    $invitations = $controller->get_ceremonies_invitations_for_student();
}

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/profile/styles.css"],
    [$base_url . "/components/styles/input.css"],
    ButtonComponent::get_stylesheets(),
    MessageComponent::get_stylesheets()
);

$meta = new MetadataComponent($stylesheets, array_merge(
    MessageComponent::get_scripts(),
    ["$base_url/pages/profile/script.js"]
));
echo $meta->render();
?>

<body>
    <?php echo $header->render(); ?>

    <main id="container">
        <section class="info-section">
            <h2 id="welcome-heading">Добре дошли в Alumni Hub</h2>
            <p>Тук можете да намерите информация за вашия профил</p>

            <?php
            echo <<<HTML
            <p class="entry"><strong class="entry-name">Потребителско име:</strong>
            $username</p>
            <p class="entry"><strong class="entry-name">Имейл:</strong> $email</p>
        HTML;

            if ($role === Role::Student) {
                $fn = $_SESSION["fn"];
                $student = $controller->students_service->get_student_by_fn($fn);
                if ($student !== null) {
                    $student_data = $student->to_array(true);
                    $fullname = $student_data["fullname"];
                    $degree = $student_data["degree"];
                    $fn = $student_data["fn"];
                    $grade = $student_data["grade"];
                    $graduation_year = $student_data["graduation_year"];

                    echo <<<HTML
                    <p class="entry"><strong class="entry-name">Име:</strong> $fullname</p>
                    <p class="entry"><strong  class="entry-name">Степен на образование:</strong>$degree</p>
                    <p class="entry"><strong  class="entry-name">Факултетен номер:</strong> $fn</p>
                <p  class="entry"><strong  class="entry-name">Оценка:</strong> $grade</p>
                <p  class="entry"><strong  class="entry-name">Година на завършване:</strong> $graduation_year</p>
                HTML;

                    if ($clothing_for_student !== null) {
                        echo <<<HTML
                            <p class="entry" ><strong class="entry-name">Размер на тога:</strong>
                            {$clothing_for_student->to_array()["size"]}</p>
                        HTML;
                    } else {
                        echo <<<HTML
                <div class="entry" >
                    <p><strong class="entry-name">Размер на тога:</strong></p>
                    <form id="clothes">
                        <select name="pick-size" id="pick-size">
                            <option value="" disabled selected>Избор</option>
                HTML;
                        foreach ($clothes as $size => $occurrences) {
                            $is_disabled = $occurrences === 0;
                            if ($is_disabled) {
                                echo "<option value=\"$size\" disabled> $size </option>";
                            
                            } else {
                                echo "<option value=\"$size\"> $size </option>";
                            }
                        }
                        echo "</select>";
                        $submit_button = new ButtonComponent("Потвърди", ButtonStyleType::Primary, true);
                        echo $submit_button->render();
                        echo <<<HTML
                </form>
                    </div>
                HTML;
                    }
                }
            }
            ?>
        </section>
        <?php
            if (isset($fn)) {
                $html = <<<HTML
                    <section class="info-section">
                        <hr/>
                        <h2>Покани</h2>
                HTML;

                foreach ($invitations as $i => $invitation) {
                    $date = $invitation["date"];
                    $speach_status = $invitation["speach_status"];
                    $accepted = $invitation["accepted"];
                    $responsibility_status = $invitation["responsibility_status"];
                    $ceremony_id = $invitation["ceremony_id"];

                    $html .= <<<HTML
                        <div class="invitation">
                        <p class="entry"><strong class="entry-name">Дата на церемонията:</strong>$date</p>
                    HTML;

                    if (!isset($accepted))
                    {
                        $accept_invitation_link = new ButtonComponent("Да", ButtonStyleType::Small, false, "accept-invitation-btn", $ceremony_id);
                        $decline_invitation_link = new ButtonComponent("Не", ButtonStyleType::Small, false, "decline-invitation-btn", $ceremony_id);

                        $html .= "<p class=\"entry\"><strong class=\"entry-name\">Приемате ли поканата:</strong>";
                        $html .= $accept_invitation_link->render() . $decline_invitation_link->render() . "</p>";
                    }

                    if ($accepted) {
                        $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие приехте тази покана</strong></p>";
                    }

                    if ($accepted === 0) {
                        $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие отказахте тази покана</strong></p>";
                    }

                    if (!isset($accepted) || $accepted) {
                        if ($speach_status === SpeachStatus::Waiting) {
                            $accept_speach_link = new ButtonComponent("Да", ButtonStyleType::Small, false, "accept-speach-btn", $ceremony_id);
                            $decline_speach_link = new ButtonComponent("Не", ButtonStyleType::Small, false, "decline-speach-btn", $ceremony_id);

                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Приемате ли да изнасяте реч:</strong>";
                            $html .= $accept_speach_link->render() . $decline_speach_link->render() . "</p>";
                        } else if ($speach_status === SpeachStatus::Accepted) {
                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие приехте да изнасяте реч</strong></p>";
                        } else if ($speach_status === SpeachStatus::Declined) {
                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие отказахте да изнасяте реч</strong></p>";
                        }

                        if ($responsibility_status === ResponsibilityStatus::WaitingDiplomas) {
                            $accept_diplomas_link = new ButtonComponent("Да", ButtonStyleType::Small, false, "accept-diplomas-btn", $ceremony_id);
                            $decline_diplomas_link = new ButtonComponent("Не", ButtonStyleType::Small, false, "decline-diplomas-btn", $ceremony_id);

                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Приемате ли да отговаряте за дипломите:</strong>";
                            $html .= $accept_diplomas_link->render() . $decline_diplomas_link->render() . "</p>";
                        }
                        else if ($responsibility_status === ResponsibilityStatus::WaitingRobes) {
                            $accept_robes_link = new ButtonComponent("Да", ButtonStyleType::Small, false, "accept-robes-btn", $ceremony_id);
                            $decline_robes_link = new ButtonComponent("Не", ButtonStyleType::Small, false, "decline-robes-btn", $ceremony_id);

                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Приемате ли да отговаряте за тогите:</strong>";
                            $html .= $accept_robes_link->render() . $decline_robes_link->render() . "</p>";
                        }
                        else if ($responsibility_status === ResponsibilityStatus::WaitingSignatures) {
                            $accept_signatures_link = new ButtonComponent("Да", ButtonStyleType::Small, false, "accept-signatures-btn", $ceremony_id);
                            $decline_signatures_link = new ButtonComponent("Не", ButtonStyleType::Small, false, "decline-signatures-btn", $ceremony_id);

                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Приемате ли да отговаряте за подписите:</strong>";
                            $html .= $accept_signatures_link->render() . $decline_signatures_link->render() . "</p>";                            
                        }
                        else if ($responsibility_status === ResponsibilityStatus::AcceptedDiplomas) {
                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие приехте да отговаряте за дипломите</strong></p>";
                        }
                        else if ($responsibility_status === ResponsibilityStatus::AcceptedRobes) {
                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие приехте да отговаряте за тогите</strong></p>";
                        }
                        else if ($responsibility_status === ResponsibilityStatus::AcceptedSignatures) {
                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие приехте да отговаряте за подписите</strong></p>";
                        }
                        if ($responsibility_status === ResponsibilityStatus::DeclinedDiplomas) {
                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие отказахте да отговаряте за дипломите</strong></p>";
                        }
                        else if ($responsibility_status === ResponsibilityStatus::DeclinedRobes) {
                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие отказахте да отговаряте за тогите</strong></p>";
                        }
                        else if ($responsibility_status === ResponsibilityStatus::DeclinedSignatures) {
                            $html .= "<p class=\"entry\"><strong class=\"entry-name\">Вие отказахте да отговаряте за подписите</strong></p>";
                        }
                    }

                    $html .= "</div>";
                    if ($i !== count($invitations) - 1) {
                        $html .= "<hr />";
                    }
                }
                
                echo $html . "</section>";
            }
        ?>

        <?php echo $message->render(); ?>

    </main>

    <?php echo $footer->render(); ?>
</body>

</html>