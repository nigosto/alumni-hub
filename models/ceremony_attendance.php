<?php
require_once __DIR__ . "/imodel.php";

enum SpeachStatus: string
{
    case None = "none";
    case Waiting = "waiting";
    case Declined = "declined";
    case Accepted = "accepted";
}

function speach_status_invite_string($speach_status)
{
    switch ($speach_status) {
        case SpeachStatus::None:
            return 'няма';
        case SpeachStatus::Waiting:
            return 'подадена';
        case SpeachStatus::Declined:
            return 'отказана';
        case SpeachStatus::Accepted:
            return 'приета';
        default:
            throw new Exception("Невалиден статус за реч");
    }
}

enum ResponsibilityStatus: string
{
    case None = "none"; 
    case WaitingRobes = "waiting_robes"; 
    case WaitingSignatures = "waiting_signatures";
    case WaitingDiplomas = "waiting_diplomas";
    case AcceptedRobes = "accepted_robes";
    case AcceptedSignatures = "accepted_signatures"; 
    case AcceptedDiplomas = "accepted_diplomas";
    case DeclinedRobes = "declined_robes";
    case DeclinedSignatures = "declined_signatures";
    case DeclinedDiplomas = "declined_diplomas"; 
}

function responsibility_status_invite_string($responsibility_status)
{
    switch ($responsibility_status) {
        case ResponsibilityStatus::None:
            return 'няма';
        case ResponsibilityStatus::WaitingRobes:
            return 'подадена, за отговорник за тоги';
        case ResponsibilityStatus::WaitingSignatures:
            return 'подадена, за отговорник за подписи';
        case ResponsibilityStatus::WaitingDiplomas:
            return 'подадена, за отговорник за връчване на дипломи';
        case ResponsibilityStatus::DeclinedRobes:
            return 'отказана, за отговорник за тоги';
        case ResponsibilityStatus::DeclinedSignatures:
            return 'отказана, за отговорник за подписи';
        case ResponsibilityStatus::DeclinedDiplomas:
            return 'отказана, за отговорник за връчване на дипломи';
        case ResponsibilityStatus::AcceptedRobes:
            return 'приета, за отговорник за тоги';
        case ResponsibilityStatus::AcceptedSignatures:
            return 'приета, за отговорник за дипломни подписи';
        case ResponsibilityStatus::AcceptedDiplomas:
            return 'приета, за отговорник за връчване на дипломи';
        default:
            throw new Exception("Невалиден статус за отговорности!");
    }
}

class CeremonyAttendance implements IModel
{
    private $ceremony_id;
    private $student_fn;
    private $accepted;
    private $speach_status;
    private $responsibility_status;

    function __construct($ceremony_id, $student_fn, $accepted, $speach_status, $responsibility_status)
    {
        $this->ceremony_id = $ceremony_id;
        $this->student_fn = $student_fn;
        $this->accepted = $accepted;
        $this->speach_status = $speach_status instanceof SpeachStatus ? $speach_status : SpeachStatus::tryFrom($speach_status) ?? parse_speach_status($speach_status);
        $this->responsibility_status = $responsibility_status instanceof ResponsibilityStatus ? $responsibility_status : ResponsibilityStatus::tryFrom($responsibility_status) ?? parse_responsibility_status($responsibility_status);
    }

    public function to_array()
    {
        return [
            "ceremony_id" => $this->ceremony_id,
            "student_fn" => $this->student_fn,
            "accepted" => $this->accepted,
            "speach_status" => $this->speach_status->value,
            "responsibility_status" => $this->responsibility_status->value,
        ];
    }

    public function get_accepted()
    {
        return $this->accepted;
    }

    public function set_accepted($val)
    {
        $this->accepted = $val;
    }

    public function get_speach_status()
    {
        return $this->speach_status;
    }

    public function set_speach_status($val)
    {
        $this->speach_status = $val;
    }

    public function get_responsibility_status()
    {
        return $this->responsibility_status;
    }

    public function set_responsibility_status($val)
    {
        $this->responsibility_status = $val;
    }

    public static function labels_ceremony_students_list()
    {
        return ["Факултетен номер", "Степен", "Имена", "Година на завършване", "Размер на тоги", "Покана за изнасяне на реч?", "Покана за отговорник?"];
    }
}
?>