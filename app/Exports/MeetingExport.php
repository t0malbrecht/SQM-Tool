<?php

namespace App\Exports;

use App\Claim;
use App\Meeting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MeetingExport implements FromCollection, WithHeadings
{
    protected $meeting;
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }
    public function collection()
    {
        $claims_with_onetimepayments = Claim::with(['oneTimePayment.favoredFundsCenter', 'oneTimePayment.chargedFundsCenter'])
            ->where('meeting_id', '=', $this->meeting->id)->has('oneTimePayment')->get();
        $claims_with_ongoingpayments = Claim::with(['ongoingPayment.favoredFundsCenter', 'ongoingPayment.chargedFundsCenter'])
            ->where('meeting_id', '=', $this->meeting->id)->has('ongoingPayment')->get();
        $claims = $claims_with_onetimepayments->merge($claims_with_ongoingpayments);

        $collection = collect([]);
        foreach ($claims as $claim){
            $date = explode('-', $claim->meeting->date);
            $germanDateFormat = $date[2].'.'.$date[1].'.'.$date[0];
            $lastTwoDigitsOfYear = substr($date[0], -2);
            $temp = collect([]);

            $temp->push($germanDateFormat);
            $temp->push('SK II: ' . $germanDateFormat .
                ' TOP 2.1.' . $claim->ioa .
                ', SKII/' . $claim->printNumber . '/' . $lastTwoDigitsOfYear);
            $temp->push('SK II: ' . $germanDateFormat .
                ' TOP 2.1.' . $claim->ioa .
                ', SKII/' . $claim->printNumber . '/' . $lastTwoDigitsOfYear);
            if($claim->oneTimePayment == null){
                $temp->push($claim->ongoingPayment->favoredFundsCenter->fundsCenterNumber);
                $temp->push($claim->ongoingPayment->favoredFundsCenter->fond);
                $temp->push($claim->ongoingPayment->chargedFundsCenter->fundsCenterNumber);
                $temp->push($claim->ongoingPayment->chargedFundsCenter->fond);
                $temp->push($claim->ongoingPayment->ongoingPaymentHistories->sum('grantedFunds'));
            }else{
                $temp->push($claim->oneTimePayment->favoredFundsCenter->fundsCenterNumber);
                $temp->push($claim->oneTimePayment->favoredFundsCenter->fond);
                $temp->push($claim->oneTimePayment->chargedFundsCenter->fundsCenterNumber);
                $temp->push($claim->oneTimePayment->chargedFundsCenter->fond);
                $temp->push($claim->oneTimePayment->grantedFunds);
            }
            $collection->push($temp);
        }
        return $collection;
    }

    public function headings(): array
    {
        return [
            'Datum', 'Kopftext', 'Positionstext', 'Sender-Finanzstelle', 'Sender-Fonds', 'Empfänger-Finanzstelle',
            'Empfänger-Fonds', 'Betrag'
        ];
    }

}
