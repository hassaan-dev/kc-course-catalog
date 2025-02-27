document.addEventListener("DOMContentLoaded", async () => {
    const categoriesRes = await fetch("http://api.cc.localhost/categories");
    const categories = await categoriesRes.json();

    const coursesRes = await fetch("http://api.cc.localhost/courses");
    let courses = await coursesRes.json();

    // Function to count courses (including child categories)
    function countCourses(categoryId) {
        let count = 0;
        categories.forEach(cat => {
            if (cat.id === categoryId || cat.parent_id === categoryId) {
                count += cat.count_of_courses;
            }
        });
        return count;
    }

    // Recursive function to create a nested category tree with toggle icons
    function createCategoryTree(parentId = null) {
        let html = "<ul>";
        categories
            .filter(cat => cat.parent_id === parentId)
            .forEach(cat => {
                const hasChildren = categories.some(subCat => subCat.parent_id === cat.id);
                html += `
                    <li>
                        <div class="category" data-id="${cat.id}">
                            ${hasChildren ? '<span class="toggle-icon">▶</span>' : ''} 
                            ${cat.name} (${countCourses(cat.id)})
                        </div>
                        <ul class="nested-category">${createCategoryTree(cat.id)}</ul>
                    </li>`;
            });
        return html + "</ul>";
    }

    function displayCategories() {
        document.getElementById("categories").innerHTML = createCategoryTree();
    }

    function displayCourses(filteredCourses = courses) {
        let html = "";
        filteredCourses.forEach(course => {
            html += `
            <div class="course-card">
                <img src="${course.preview}" alt="${course.name}">
                <div class="course-info">
                    <span class="category-tag">${course.main_category_name || "Unknown Category"}</span>
                    <h3>${course.name}</h3>
                    <p>${course.description.substring(0, 100)}...</p>
                </div>
            </div>`;
        });
        document.getElementById("courses").innerHTML = html;
    }

    document.getElementById("categories").addEventListener("click", (e) => {
        if (e.target.classList.contains("category") || e.target.classList.contains("toggle-icon")) {
            const categoryDiv = e.target.closest(".category");
            const categoryId = categoryDiv.getAttribute("data-id");

            const filteredCourses = courses.filter(c => c.category_id === categoryId);
            displayCourses(filteredCourses);

            // Toggle child categories visibility
            const parentLi = categoryDiv.parentElement;
            const subList = parentLi.querySelector("ul");
            if (subList) {
                const toggleIcon = parentLi.querySelector(".toggle-icon");
                subList.style.display = subList.style.display === "none" ? "block" : "none";
                toggleIcon.textContent = subList.style.display === "none" ? "▶" : "▼";
            }
        }
    });

    displayCategories();
    displayCourses();
});