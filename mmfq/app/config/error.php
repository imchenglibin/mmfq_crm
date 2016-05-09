<?php
return array(
  'success' => array('code' => 0, 'message' => '成功'),
  'logInFailed' => array('code' => 1, 'message' => '登录失败，用户名或者密码错误'),
  'authenticationFailed' => array('code' => 2, 'message' => '用户未登录'),
  'serverInternalError' => array('code' => 3, 'message' => '服务器内部错误'),
  'invalidPassword' => array('code' => 4, 'message' => '密码不合法'),
  'invalidUsername' => array('code' => 5, 'message' => '用户名不合法'),
  'forbidded' => array('code' => 6, 'message' => '权限不够'),
  'userNotExists' => array('code' => 7, 'message' => '用户不纯在'),
  'invalidRealName' => array('code' => 8, 'message' => '真实姓名不合法'),
  'invalidParams' => array('code' => 9, 'message' => '参数不合法'),
  'customerNotExists' => array ('code' => 10, 'message' => '客户不纯在'),
  'returnVisitRecordNotExists' => array ('code' => 10, 'message' => '回访纪录不存在'),
  'userExists' => array ('code' => 11, 'message' => '用户已经存在'),
  'projectKindExists' => array ('code' => 12, 'message' => '标签已经存在'),
  'projectKindNotExists' => array ('code' => 13, 'message' => '标签不存在'),
  'projectNotExists' => array ('code' => 13, 'message' => '项目不存在'),
  'notFound' => array ('code' => 14, 'message' => '请求不纯在'),
  'deleteSelf' => array ('code' => 15, 'message' => '不能删除自己')
)
?>
