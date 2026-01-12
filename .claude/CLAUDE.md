# Talent Arena App API

## プロジェクト概要

学生スポーツにおける選手(プレイヤー)とチームのスカウトマッチング バックエンド

## 技術スタック

-   **フレームワーク**: Laravel 10
-   **PHP**: 8.1 以上
-   **フロントエンドビルド**: Vite + TailwindCSS 4
-   **テスト**: PHPUnit 10
-   **コードフォーマット**: Laravel Pint

## 開発コマンド

```bash
# 開発サーバー起動（server, queue, vite を同時起動）
composer dev

# テスト実行
composer test

# コードフォーマット
./vendor/bin/pint

# 個別のArtisanコマンド
php artisan serve          # APIサーバー
php artisan migrate        # マイグレーション実行
php artisan make:model     # モデル生成
php artisan make:controller # コントローラー生成
```

## ディレクトリ構造

```
app/
├── Consts/          # 定数クラス
├── Http/
│   ├── Controllers/ # コントローラー
│   └── Requests/    # フォームリクエスト（バリデーション）
├── Models/          # Eloquentモデル
├── Providers/       # サービスプロバイダ
└── Services/        # ビジネスロジック

routes/
├── api.php          # APIルート定義
├── web.php          # Webルート
└── console.php      # Artisanコマンド

database/
├── migrations/      # マイグレーション
├── factories/       # モデルファクトリ
└── seeders/         # シーダー

tests/
├── Feature/         # 機能テスト
└── Unit/            # ユニットテスト
```

## 主要なモデル・定数

-   `Player`: 選手
-   `AuthKey`: 認証キー（仮登録用）
-   `CommonConsts::SUBJECT_TYPE_PLAYERS`: 選手タイプ (0)
-   `CommonConsts::SUBJECT_TYPE_TEAMS`: チームタイプ (1)
-   ステータス: `IS_TMP_MEMBER`(0), `IS_MEMBER`(1), `IS_LEAVE_MATCH_MEMBER`(2), `IS_LEAVE_MEMBER`(3)

## API エンドポイント

-   `/api/register/` 配下で選手・チーム登録機能を提供

## コーディング規約

-   PSR-12 準拠
-   Laravel Pint でフォーマット
-   コントローラーはシンプルに、ビジネスロジックは Service クラスへ
-   バリデーションは FormRequest（`app/Http/Requests/`）を使用

## Claude への指示

-   **仕様を受け取ったら、まず実装の方向性を提案する**（いきなりコードを書かない、自走力を持つ）
-   検討してもわからない場合は、実装を行い **実装意図・修正意図を説明する**
-   コードを修正した際は、必ず **修正意図（なぜその変更を行ったか）** を説明すること
