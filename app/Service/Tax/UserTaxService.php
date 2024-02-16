<?phpnamespace App\Service\Tax;use Illuminate\Support\Collection;class UserTaxService{    public static function calculateTax(Collection $taxItems, $earning, $method)    {        $result = $earning;        if ($method == 'old') $taxItems = $taxItems->sortBy('end_subtraction');        elseif ($method == 'new') $taxItems = $taxItems->sortBy('order');        foreach ($taxItems as $item) {            if ($item->end_subtraction == 0) {                $result -= $item->is_percent ? $earning * $item->value / 100 : $item->value;            } else {                $result -= $item->is_percent ? $result * $item->value / 100 : $item->value;            }        }        return round($earning - $result); // Sum taxes    }}