<!doctype html>
<html lang="zh-CN">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  </head>
<body>
  <p>
  {{$name}}你好，项目经理{{$manager_name}}在<b>{{$project_name}}</b>中添加了一个新任务<b style="color:red">{{$task_name}}</b>，并在任务中提到了你，任务详细情况如下，请及时到项目管理系统领取任务，任务详细说明如下:
  </p>
  <p>
  <em>任务名</em>：<b>{{$task_name}}</b></br>
  <em>任务类型</em>：<b>{{$type}}</b></br>
  <em>任务难度</em>：<b>{{$task_difficulty}}</b></br>
  <em>项目估时</em>：<b>{{$etimated_time}}</b>天</br>
  <em>任务优先级</em>：<b>{{$task_priority}}</b></br>
  <em>任务权重</em>：<b>{{$task_weight}}</b></br>
  <em>项目经理</em>：<b>{{$manager_name}}</b></br>
  <em>项目描述</em>：<b>{{$description}}</b></br>
  </p>
  <p>
    附件：</br>

  </p>
</body>
</html>
