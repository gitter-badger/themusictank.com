<?php

class Chart extends AppModel
{	
    public function generate($object, $startTimestamp, $endTimestamp, $qty)
    {
        $weekNo = date("W", $endTimestamp);
        $year = date("Y", $endTimestamp);
        $chart = $object->getSpan($startTimestamp, $endTimestamp, $qty);
        $lcAlias = strtolower($object->alias);
       // $lastWeek = $weekNo - 1;
        
        $formatted = array();
        foreach($chart as $idx => $row)
        {
            $formatted[] = array(
                "rank" => $idx + 1,
                "weekno" => $weekNo,
                "year" => $year,
                "$lcAlias" => $row[$object->alias]["id"]
            );
        }
        
        return $formatted;
    }
    
    public function saveWeekly($charts) 
    {
        return $this->saveMany($charts);
    }
    
} 



        
        /*
        $queries = array();
        foreach($chart as $idx => $row)
        {
            / *
            $queries[] = "
                (SELECT 
                    qry1.weeks_on_chart as row{$idx}_weeks_on_chart, 
                    qry1.top_rank as row{$idx}_top_rank, 
                    qry2.last_week_rank as row{$idx}_last_week_rank
                    
                    FROM                
                    (SELECT 
                        COUNT(id) AS weeks_on_chart,
                        MAX(rank) AS top_rank
                    FROM charts
                    WHERE {$lcAlias}_id = {$row[$object->alias]["id"]}) as qry1,
                    
                    (SELECT
                        rank as last_week_rank
                    FROM charts
                    WHERE
                        {$lcAlias}_id = {$row[$object->alias]["id"]}
                        AND weekno = $lastWeek) as qry2
                ) as topqry{$idx}
            ";
                     * /
            $queries[] = $row[$object->alias]["id"];
        }
        
        
        return $this->query("
            SELECT * FROM 
                (SELECT 
                    COUNT(id) AS weeks_on_chart,
                    MAX(rank) AS top_rank
                FROM charts
                WHERE {$lcAlias}_id IN (". implode(", ", $queries) .")) as qry1,
                    
                (SELECT
                    rank as last_week_rank
                FROM charts
                WHERE
                    {$lcAlias}_id = {$row[$object->alias]["id"]}
                    AND weekno = $lastWeek) as qry2;");*/