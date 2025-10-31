# 💥 MrBoom — Automotive Shop Management System

**MrBoom** is a complete management system for **automotive accessory and window tint (insulfilm) shops**, developed with **Symfony (PHP)**.  
It helps businesses manage **customers, vehicles, services, and product inventory** — optimizing operations and improving service efficiency.

---

## 🚗 Main Features

-   👥 **Customer and vehicle management** (registration, history, and contact info)
-   🧾 **Service orders**: create, edit, and track ongoing and completed jobs
-   🛠️ **Product and accessory catalog** with price and stock control
-   💰 **Sales and service reports** for performance insights
-   🔔 **Status tracking** for each service (in progress, completed, delivered)
-   ⚙️ **User-friendly interface** built with Twig and Bootstrap
-   🔐 Authentication and role-based access (admin, employee, etc.)

---

## 🧰 Tech Stack

**Backend:** PHP 8+, Symfony  
**Frontend:** Twig templates, HTML5, CSS3, JavaScript, Bootstrap  
**Database:** MySQL  
**Tools:** Composer, Git, WSL, VS Code  
**Architecture:** MVC (Model-View-Controller)

---

## ⚙️ Installation & Setup

1. **Clone the repository**

    ```bash
    git clone https://github.com/gustavoorgb/projeto-mrboom.git
    cd projeto-mrboom
    ```

2. **Install dependencies**

    ```bash
    composer install
    ```

3. **Configure environment variables**
   Copy the example environment file:

    ```bash
    cp .env.example .env
    ```

    Then update your database credentials:

    ```
    DATABASE_URL="mysql://user:password@127.0.0.1:3306/mrboom"
    ```

4. **Run migrations**

    ```bash
    php bin/console doctrine:migrations:migrate
    ```

5. **Start the Symfony development server**

    ```bash
    symfony serve
    ```

6. **Access the system**
   👉 [http://localhost:8000](http://localhost:8000)

---

## 🧠 Project Structure

```
projeto-mrboom/
├── config/         # Routes, services, and environment configs
├── src/            # Controllers, Entities, Repositories, Services
├── templates/      # Twig templates for UI rendering
├── public/         # Static assets and entry point
└── migrations/     # Database schema definitions
```

---

## 📊 Example Modules

| Module        | Description                                  |
| ------------- | -------------------------------------------- |
| **Customers** | Register and manage clients                  |
| **Vehicles**  | Track client vehicles and linked services    |
| **Services**  | Manage insulfilm and accessory installations |
| **Products**  | Manage inventory and pricing                 |
| **Orders**    | Create and monitor work orders               |
| **Reports**   | Analyze sales and performance data           |

---

## 🧩 Future Improvements

-   Integration with WhatsApp API for customer communication
-   Online appointment scheduling for clients
-   Dashboard with sales and performance analytics
-   Multi-branch (multi-store) support
-   Dark mode interface 🎨

---

## 👨‍💻 Author

**Gustavo Gabry Orçay**  
Fullstack PHP Developer — Brazil  
📧 gustavogabry24@gmail.com  
🌐 [GitHub Profile](https://github.com/gustavoorgb)

---

## 🗣️ About the Project

The **MrBoom System** was developed to help small and medium-sized automotive shops efficiently manage their day-to-day operations.  
It brings digital organization to a market that still relies heavily on manual processes, offering a clear and responsive interface to control services, customers, and products.

---

### 💡 Keywords for recruiters

`PHP` • `Symfony` • `Fullstack Developer` • `Automotive Management System` • `Shop Management` • `Inventory` • `Orders` • `Bootstrap` • `MySQL` • `MVC` • `OOP` • `Clean Code`
