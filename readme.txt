=== Attendance Manager ===
Contributors: tnomi
Donate link: 
Tags: schedule, attendance, work, employee, online scheduling
Requires at least: 4.1
Tested up to: 4.2.3
Stable tag: 0.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Each user can do attendance management by themselves. 
管理者のほか、ユーザー自身も編集可能な出勤管理プラグイン。

== Description ==

An administrator can do all users’ attendance management.<br>
And each user can do attendance management by themselves.

An attendance schedule is displayed by shortcords.<br>
* Today's staff<br>
* Weekly schedule<br>
* Monthly schedule<br>


管理者は全てのユーザーの出勤管理ができます。<br>
また、ユーザーも自分自身の出勤管理が可能です。<br>

出勤スケジュールはショートコードで表示されます。<br>
* 今日の出勤スタッフ<br>
* 週間スケジュール<br>
* 月間スケジュール<br>

== Installation ==

This plug-in makes several pages and data base tables automatically.<br>
このプラグインはいくつかのページとデータベーステーブルを自動的に作ります。

= Installation =

1. Donwload plugin file (“attendance-manager.zip”)<br>
プラグインファイル (“attendance-manager.zip”) をダウンロードします。

2. Upload plugin file from Administrator menu “Plugins > Add New > Upload Plugin”.<br>
管理画面「プラグイン > 新規追加 > プラグインのアップロード」からプラグインファイルをアップロードします。

3. Activate the plugin.<br>
プラグインを有効化します。

= Plugin set up =

1. Open the WordPress admin panel, and go to the plugin option page “Attendance Manager”.<br>
管理画面を開き「Attendance Manager」メニューを開きます。

2. Set up option item of some.<br>
オプション項目を設定します。

= User registration as "staff" =

1. Register staff of your workplace as user.<br>
職場のスタッフをユーザー登録します。

2. When registering user, check "This user is a staff".<br>
登録の際、「このユーザーはスタッフです」をチェックします。

= Post each staff’s introduction article =

Post each staff's introduction article. (For example into a "staff" category etc.)<br>
And insert short cord [attmgr_weekly id="xx"] to that article.<br>

* "id" is ID number of each user in your WordPress.

新たに各スタッフの紹介記事を投稿します。（例えば「スタッフ」カテゴリーなどに）
その記事に、ショートコード [attmgr_weekly id="xx"] を挿入します。

* "id" はあなたのサイトにおける各ユーザーのID番号です。

= Post a staff’s information =

Post each staff’s information article. (For example, into a “staff” category etc.)<br>
And insert short cord [attmgr_weekly id=”xx”] to that article.<br>
This short code displays the weekly schedule of this staff.<br>

各スタッフの紹介記事を投稿します。（例えば「スタッフ」カテゴリーなどに）<br>
その記事に、ショートコード [attmgr_weekly id=”xx”] を挿入します。<br>
このショートコードは、そのスタッフの週間スケジュールを表示するものです。<br>

* “id” is ID number of each user in your WordPress.<br>
“id” はあなたのサイトにおける各ユーザーのID番号です。

= Attendance management =

* An administrator does all the user's attendance management by a scheduler for admin.<br>
管理者は管理者用スケジューラから全てのユーザーの出勤管理を行ないます。

* A staff logs in and does the attendance management by a scheduler for a staff.<br>
スタッフはログインしてスタッフ専用スケジューラから自身の出勤管理を行ないます。

== Frequently Asked Questions ==

If you encounter some problems, please ask me.

== Screenshots ==

1. "Scheduler for Admin" page 
2. "Scheduler for Staff" page 
3. "Today's Staff" page
4. "Weekly schedule" page 
5. "Monthly schedule" page 
6. Plugin option 

== Changelog ==

= 0.2.0 =

* first release.

== Upgrade Notice ==

= 0.2.0 =

first release.