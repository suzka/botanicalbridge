<?php

class MyQuery
{
    public $query = '';

    public $search_url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?';
    public $fetch_url  = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?';

    public $search_parameters = array(
        'db'                  => 'pubmed',
        'term'                => '',
        'retmode'             => 'xml',
        'retstart'            => '0',
        'retmax'              => '5',
        'usehistory'          => 'y',
        'sort'                => 'relevance',
        'field'               => '[TIAB]',
        'pmfilter_Subsets'    =>  'Complementary%20Medicine'
    );

    public $fetch_parameters = array(
        'db'             => 'pubmed',
        'retmax'         => '5',
        'retmode'        => 'xml', //'text', //return text abstracts, defult is "xml" records
        //'rettype'        => 'abstract', //record view returned
        'query_key'      => '',
        'WebEnv'         => ''
    );

    public $search_results;
    public $fetch_results;
    public $matches = array();

    public $match_regex = '/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/';

    public function __constructor()
    {
        $this->query = $query;
    }

    public function search( $query )
    {
        echo "<h2 class='pubmed'>PubMed Articles About " . $query . "</h2></br>";
        $cite = "<p>Source: National Center for Biotechnology Information (NCBI)[Internet]. Bethesda (MD): National Library of Medicine (US), National Center for Biotechnology Information; [1988] – [cited 2018 Apr 5]. Available from: <a href='https://www.ncbi.nlm.nih.gov/'>https://www.ncbi.nlm.nih.gov/</a></p>";
        echo $cite;
        $this->search_parameters['term'] = "(" . $query;
        // narrow search query to human clinical trials and reviews

        $this->search_parameters['term'] .= " AND (( Clinical Trial[ptyp] OR systematic[sb] ) AND Humans[Mesh] AND cam[sb]))";
        //echo $this->search_parameters['term'];
        $url = $this->search_url . http_build_query( $this->search_parameters );
        $this->search_results = simplexml_load_file( $url );
        
    }

    public function fetch() 
    {
        $this->fetch_parameters['query_key'] = (string) $this->search_results->QueryKey;
        $this->fetch_parameters['WebEnv']    = (string) $this->search_results->WebEnv;
        $url = $this->fetch_url . http_build_query( $this->fetch_parameters );
        $this->fetch_results = file_get_contents( $url );
    }


    public function parse()
    {
        $citations = "";

        // parse xml and extract data points
        $xml = simplexml_load_string($this->fetch_results) or die("Error: Cannot create object");
        // scroll through results and parse individual articles
        foreach ($xml->PubmedArticle as $pmarticle) {
            $citations .= "<p style='font-size:medium'>";
            foreach ($pmarticle->MedlineCitation as $citation) {
                foreach ($citation->Article->AuthorList->Author as $list ) {
                    $citations .= $list->LastName . ", " . $list->Initials . "., ";
                }
                $citations .= '(' . $citation->DateRevised->Year . ") ";
                
                //foreach ($citation->Article->ELocationID as $link) {
                //    $citations .= $link->doi . "Is it missing?";
                //}
                $doi = $citation->Article->ELocationID;
                $citations .= "<em><a href='https://doi.org/" . $doi . "'>" . $citation->Article->ArticleTitle . "</a></em>";
                $citations .= "<p style='font-size:small'>" . $citation->Article->Abstract->AbstractText  . "</p>";
            }
            $citations .= "</p>";
        }

        //$matches = array();
        //preg_match_all( $this->match_regex, $this->fetch_results, $matches );
        //$this->matches = array_values( $matches[0] );
        $this->citations = $citations;

        echo $citations;
    }

    public function get($query)
    {
        $this->search($query);
        $this->fetch();
        $this->parse();
        return $this->citations;
    }
}

// Turn this off for go live!!
//$query  = new MyQuery();
//$result = $query->get("(Lavandula angustifolia AND ( Clinical Trial[ptyp] OR systematic[sb] )  AND 'last 5 years'[PDat] AND Humans[Mesh] AND cam[sb]))");
//$result = $query->get($_GET['genus'] . " " . $_GET['species']);
//echo $result;

?>