<?php
    require_once __DIR__ . '/../../admin/views/test-view-list.php';

    class Test_View_Test extends Base_UnitTestCase {
        function testRender(){
            $expected_html_output = '<h1>Testen</h1>1 Eerste test Dit is een eerste testje 4 2014-03-16 2014-03-17 ' .
                '2014-03-17 2014-04-17 1 3 1<br>2 Tweede test Dit is een tweede test 0 2014-03-14 2014-03-17 null '.
                'null 2 1 2<br>';
            $data = array(
                array(1, 'Eerste test', 'Dit is een eerste testje', 4, '2014-03-16', '2014-03-17', '2014-03-17',
                    '2014-03-17', 1, 3, 1),
                array(2, 'Tweede test', 'Dit is een tweede test', 0, '2014-03-14', '2014-03-17', 'null',
                    'null', 2, 1, 2),
            );

            $html_output = Test_View_List::render($data);
            $this->assertEquals($expected_html_output, $html_output);
        }
    }