<?php
require_once __DIR__ . "/imodel.php";

enum SpeachStatus: string
{
    case None = "none";
    case Waiting = "waiting";
    case Declined = "declined";
    case Accepted = "accepted";
}

function parse_speach_status($speach_status)
{
    $speach_status = mb_strtolower($speach_status);
    switch ($speach_status) {
        case 'няма':
            return SpeachStatus::None;
        case 'изчакващ':
            return SpeachStatus::Waiting;
        case 'отказал':
            return SpeachStatus::Declined;
        case 'приел':
            return SpeachStatus::Accepted;
        default:
            throw new Exception("Невалиден статус за речи!");
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

function parse_responsibility_status($responsibility_status)
{
    $responsibility_status = mb_strtolower($responsibility_status);
    switch ($responsibility_status) {
        case 'няма':
            return ResponsibilityStatus::None;
        case 'изчакващ отговорник за тоги':
            return ResponsibilityStatus::WaitingRobes;
        case 'изчакващ отговорник за подписи':
            return ResponsibilityStatus::WaitingSignatures;
        case 'изчакващ отговорник за връчване на дипломи':
            return ResponsibilityStatus::WaitingDiplomas;
        case 'отказал отговорник за тоги':
            return ResponsibilityStatus::DeclinedRobes;
        case 'отказал отговорник за подписи':
            return ResponsibilityStatus::DeclinedSignatures;
        case 'отказал отговорник за връчване на дипломи':
            return ResponsibilityStatus::DeclinedDiplomas;
        case 'приел отговорник за тоги':
            return ResponsibilityStatus::AcceptedRobes;
        case 'приел отговорник за подписи':
            return ResponsibilityStatus::AcceptedSignatures;
        case 'приел отговорник за връчване на дипломи':
            return ResponsibilityStatus::AcceptedDiplomas;
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
}
?>