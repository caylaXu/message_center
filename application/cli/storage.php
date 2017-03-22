<?php
class Storage
{
    private $mysql;         //MySQL连接

    private $redis;         //Redis连接

    private $cur_table;     //当前表

    private $cur_suffix;    //当前表后缀

    private $surplus;       //剩余可插入的条数

    private $records;

    public function __construct()
    {
        $this->records = 1000000;
        //初始化数据库连接
        $this->_init_mysql();
        //初始化Redis连接
        $this->_init_redis();
        //获取当前表信息
        $this->_get_cur_table_info();
    }

    //初始化数据库连接
    private function _init_mysql()
    {
        $dsn = 'mysql:host=localhost;dbname=Message';
        $user = 'message';
        $passwd = 'zar3Is#k';
        $this->mysql = new PDO($dsn, $user, $passwd, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            PDO::MYSQL_ATTR_LOCAL_INFILE => 1,
        ));
        if (!$this->mysql)
        {
            die('连接MySQL失败');
        }
    }

    //初始化Redis连接
    private function _init_redis()
    {
        $host = '127.0.0.1';
        $port = 6379;
        $passwd = 'motouch@2015';
        $this->redis = new Redis();
        if (!$this->redis->pconnect($host, $port))
        {
            $this->redis = FALSE;
            return ;
        }
        $this->redis->auth($passwd);
    }

    //获取当前表信息
    private function _get_cur_table_info()
    {
        $this->cur_table = 'MessageLog1';
        $this->cur_suffix = 1;
        $this->surplus = $this->records;
        $sql = "SHOW TABLES LIKE 'MessageLog%'";
        $stmt = $this->_mysql_exec($sql, '获取当前表失败');
        $rows = array_column($stmt->fetchAll(), 0);

        if (!$rows || count($rows) == 1)
        {
            $sql = "CREATE TABLE MessageLog1 LIKE MessageLogNew";
            $this->_mysql_exec($sql, '新建MessageLog1失败');
            $this->cur_table = 'MessageLog1';
            $this->redis->set('surplus',$this->surplus);

            //写入映射表
            $sql = "SELECT Id FROM RltTimeTable WHERE TABLENAME='{$this->cur_table}'";
            $stmt = $this->_mysql_exec($sql, '获取映射表信息失败');
            if ($stmt->fetch())
            {
                return ;
            }
            $sql = "INSERT INTO RltTimeTable(MinTime, MaxTime, TableName)" .
                " VALUES (0, 0, '{$this->cur_table}')";
            $this->_mysql_exec($sql, '写入映射表失败');
        }
        else
        {
            foreach ($rows as $table)
            {
                $suffix = intval(substr($table,10));
                if ($suffix < 1 || $suffix > 2000)
                {
                    continue;
                }

                if ($suffix > $this->cur_suffix)
                {
                    $this->cur_suffix = $suffix;
                    $this->cur_table = $table;
                }
            }

            //查询可插入的条数,先查redis没有再查数据库
            $this->surplus = $this->redis->get('surplus');
            if(empty($this->surplus))
            {
                $sql = "SELECT COUNT(*) FROM {$this->cur_table}";
                $stmt = $this->_mysql_exec($sql, '获取当前表可插入记录数失败');
                $count = $stmt->fetch()[0];
                if ($count >= $this->records)
                {
                    $this->_create_table();
                }
                else
                {
                    $this->surplus = $this->records-$count;
                    $this->redis->set('surplus',$this->surplus);
                }
            }
        }
    }

    //创建新表
    private function _create_table()
    {
        $this->cur_suffix += 1;
        $this->cur_table = 'MessageLog' . $this->cur_suffix;
        $sql = "CREATE TABLE {$this->cur_table} LIKE MessageLogNew";
        $this->_mysql_exec($sql, '新建日志表失败');
        $this->surplus = $this->records;
        $this->redis->set('surplus',$this->records);
        //写入映射表
        $sql = "SELECT Id FROM RltTimeTable WHERE TABLENAME='{$this->cur_table}'";
        $stmt = $this->_mysql_exec($sql, '获取映射表信息失败');
        if ($stmt->fetch())
        {
            return ;
        }
        $sql = "INSERT INTO RltTimeTable(MinTime, MaxTime, TableName)" .
            " VALUES (0, 0, '{$this->cur_table}')";
        $this->_mysql_exec($sql, '写入映射表失败');
    }

    //执行入库
    public function store($data)
    {
        //加载到数据库
        $sql       = sprintf("INSERT INTO %s (Type,ToUsers,Content,Attr,ReceiptTime,SendTime,Response)VALUES(?, ?, ?, ?, ?,?,?)", $this->cur_table);
        $stmt      = $this->mysql->prepare($sql);
        $input_parameters = array(
            $data['Type'],
            $data['ToUsers'],
            $data['Content'],
            $data['Attr'],
            $data['ReceiptTime'],
            $data['SendTime'],
            $data['Response']
        );
        $result = $stmt->execute($input_parameters);

        if (!$result)
        {
            return false;
        }

        //更新映射表
        $sql = "SELECT MIN(ReceiptTime), MAX(ReceiptTime) FROM {$this->cur_table}";
        $stmt = $this->_mysql_exec($sql, '查询时间跨度失败');
        $receipt_time = $stmt->fetch();
        $sql = "UPDATE RltTimeTable SET " .
            "MinTime = {$receipt_time[0]}, MaxTime = {$receipt_time[1]} " .
            "WHERE TableName = '{$this->cur_table}'";
        $this->_mysql_exec($sql, '更新映射表失败');

        $this->surplus --;
        $this->redis->decr('surplus',1);//redis自减

        if($this->surplus <= 0)
        {
            $this->_create_table();
        }
    }

    //执行SQL语句
    private function _mysql_exec($sql, $errInfo)
    {
        $stmt = $this->mysql->prepare($sql);
        $result = $stmt->execute();
        if($result === false)
        {
            print_r($sql);
            echo "\nPDO::errorInfo():\n";
            print_r($this->mysql->errorInfo());
            die($errInfo);
        }
        return $stmt;
    }
}