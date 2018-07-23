
## 设计思路
列表的插入都采用RPUSH，这样在尾部追加，好处是可以不改变之前元素的索引.
<p>package,meeesage被看作两种对象.对象包含静态和动态属性。这也是划分缓存块的依据</p>


<p>package_list   list</p>
<p>package_pool   sorted set</p>
<p>user_message_inbox   list</p>
<p>user_message_outbox   list</p>
<p>message_pool   hash 存储message动态属性。如is_reader,read_time</p>

<p>user_proper_message_list   list 存储message动态属性。如is_reader,read_time</p>
<p>proper_message_pool   hash 存储message动态属性。如is_reader,read_time</p>


